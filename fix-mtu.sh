#!/bin/bash
# Script to fix network TLS handshake timeout in Rancher Desktop VM when using VPN on macOS.
# It sets the MTU of eth0 inside the VM to 1300.

RDCTL="/Users/tanawatnoipalee/.rd/bin/rdctl"

if [ ! -f "$RDCTL" ]; then
    echo "rdctl not found at $RDCTL. Please update the path in this script if needed."
    exit 1
fi

echo "Setting MTU of eth0 inside VM to 1300..."
$RDCTL shell sudo ip link set dev eth0 mtu 1300

echo "Done! Current network status inside VM:"
$RDCTL shell ip addr show eth0
