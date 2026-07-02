<?php

namespace App\Http\Controllers;

use App\Models\DailyHouseRecord;
use App\Models\FeedPriceMaster;
use App\Models\Flock;
use App\Support\FarmAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlockFeedSummaryController extends Controller
{
    public function __invoke(Request $request, Flock $flock): View
    {
        $user = $request->user();
        FarmAccess::ensureFlock($user, $flock);

        $flock->load(['farm', 'flockHouseStarts.house']);

        $flockOptions = FarmAccess::flocksQuery($user)
            ->with('farm')
            ->orderBy('farm_id')
            ->latest('start_date')
            ->latest('id')
            ->get();

        $farmIdsWithFlocks = $flockOptions
            ->pluck('farm_id')
            ->push($flock->farm_id)
            ->unique()
            ->values();

        $farms = FarmAccess::farmsQuery($user)
            ->whereIn('id', $farmIdsWithFlocks)
            ->orderBy('farm_name')
            ->get();

        $feedSelectorFlocks = $flockOptions
            ->map(fn (Flock $option) => [
                'value' => (string) $option->id,
                'text' => $option->flock_code.($option->farm ? ' - '.$option->farm->farm_name : ''),
                'farmId' => (string) $option->farm_id,
                'url' => route('flocks.feed-summary', $option),
                'selected' => (int) $option->id === (int) $flock->id,
            ])
            ->values();

        // Get houses for this flock
        $houses = $flock->flockHouseStarts->sortBy('house.house_no')->values();
        $houseIds = $houses->pluck('house_id')->toArray();

        // Get all feed receipt items for the houses of this flock
        $receiptItems = \App\Models\FeedReceiptHouseItem::whereIn('house_id', $houseIds)
            ->whereHas('feedReceipt', function ($query) use ($flock) {
                $query->where('farm_id', $flock->farm_id)
                      ->whereDate('receipt_date', '>=', $flock->start_date);
            })
            ->with('feedReceipt')
            ->get();

        // Find all active feed codes in FeedPriceMaster
        $masterFeedCodes = FeedPriceMaster::where('is_active', true)
            ->pluck('feed_code')
            ->map(fn($c) => rtrim(trim($c), 'tT'))
            ->toArray();

        // Find all unique normalized feed codes used in this flock's receipts
        $receiptFeedCodes = $receiptItems->pluck('feedReceipt.feed_code')
            ->filter()
            ->map(fn($c) => rtrim(trim($c), 'tT'))
            ->toArray();

        // Union and sort feed codes
        $feedCodes = collect($masterFeedCodes)
            ->merge($receiptFeedCodes)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // If no feed codes found at all, default to showing 203, 204, 205 as columns
        if (empty($feedCodes)) {
            $feedCodes = ['203', '204', '205'];
        }

        // Retrieve prices for each feed code
        $prices = [];
        foreach ($feedCodes as $code) {
            $prices[$code] = FeedPriceMaster::where('is_active', true)
                ->where('feed_code', $code)
                ->where('effective_date', '<=', $flock->start_date ?: now())
                ->orderByDesc('effective_date')
                ->first()?->price_per_kg 
                ?? FeedPriceMaster::where('is_active', true)
                    ->where('feed_code', $code)
                    ->orderByDesc('effective_date')
                    ->first()?->price_per_kg 
                ?? 0.00;
        }

        $rows = [];
        $totals = [
            'initial_birds' => 0,
            'feeds' => array_fill_keys($feedCodes, 0.0),
            'sum_feed' => 0.0,
            'feed_cost' => array_fill_keys($feedCodes, 0.0),
            'total_cost' => 0.0
        ];

        foreach ($houses as $start) {
            $houseId = $start->house_id;
            $initialBirds = (int)$start->initial_birds;
            $totals['initial_birds'] += $initialBirds;

            $houseFeeds = array_fill_keys($feedCodes, 0.0);
            foreach ($receiptItems->where('house_id', $houseId) as $item) {
                $origCode = $item->feedReceipt?->feed_code;
                $normCode = $origCode ? rtrim(trim($origCode), 'tT') : null;
                
                // If it matches one of our feed codes, sum the quantity_kg
                if ($normCode && in_array($normCode, $feedCodes)) {
                    $houseFeeds[$normCode] += (float)$item->quantity_kg;
                }
            }

            $sumFeed = array_sum($houseFeeds);
            
            // Accumulate house feeds to totals
            foreach ($feedCodes as $code) {
                $totals['feeds'][$code] += $houseFeeds[$code];
            }
            $totals['sum_feed'] += $sumFeed;

            // Calculate ratios
            $feedIntakePerBird = $initialBirds > 0 ? $sumFeed / $initialBirds : 0.0;
            $cumulativeFeed = $feedIntakePerBird; // Same in their sheet

            $feedBagsRatio = [];
            foreach ($feedCodes as $code) {
                $qtyPerBird = $initialBirds > 0 ? $houseFeeds[$code] / $initialBirds : 0.0;
                $bagsPct = $qtyPerBird / 30 * 100;
                $feedBagsRatio[$code] = [
                    'qty_per_bird' => $qtyPerBird,
                    'bags_pct' => $bagsPct
                ];
            }

            $totalQtyPerBird = $initialBirds > 0 ? $sumFeed / $initialBirds : 0.0;
            $totalBagsPct = $totalQtyPerBird / 30 * 100;

            $rows[] = [
                'house' => $start->house,
                'initial_birds' => $initialBirds,
                'feeds' => $houseFeeds,
                'sum_feed' => $sumFeed,
                'feed_intake_per_bird' => $feedIntakePerBird,
                'cumulative_feed' => $cumulativeFeed,
                'feed_bags_ratio' => $feedBagsRatio,
                'total_qty_per_bird' => $totalQtyPerBird,
                'total_bags_pct' => $totalBagsPct
            ];
        }

        // Calculate bottom total feed costs
        $totalFeedCost = 0.0;
        foreach ($feedCodes as $code) {
            $cost = $totals['feeds'][$code] * $prices[$code];
            $totals['feed_cost'][$code] = $cost;
            $totalFeedCost += $cost;
        }
        $totals['total_cost'] = $totalFeedCost;

        return view('summaries.feed', [
            'flock' => $flock,
            'farms' => $farms,
            'flockOptions' => $flockOptions,
            'feedSelectorFlocks' => $feedSelectorFlocks,
            'feedCodes' => $feedCodes,
            'prices' => $prices,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }
}
