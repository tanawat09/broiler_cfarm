with open(r"c:\Users\admin\OneDrive\Desktop\APP_Max\Broiler\BGBR-01_2704_5326_25012.txt", "r", encoding="utf-16-le") as f:
    for i in range(15):
        line = f.readline()
        if not line:
            break
        # remove BOM and print safe ascii/utf8
        line_clean = line.replace('\ufeff', '').strip()
        print(f"Row {i}: {line_clean.encode('utf-8', errors='replace').decode('utf-8')}")
