# CHANGE NOTE
## 1.Payment Gateway
	- menambahkan package snapmidtrans di apps backend
    - menambahkan integrasi snap midtrans di subscription_plan model
    - menambahkan CORS (Cross Origin Resource Sharing) agar midtrans dapat mengirimkan http notification status dan sistem otomatis
        menambahkan subscription transaction terkait.
    - menambahkan fitur untuk menerima notifikasi dari midrans, dan otomatis mengubah status transaksi subscription
    - 
## 2. Fitur print
    - menambahkan route untuk invoice sama html untuk invoice
    - styling di file blade invoice, langsung aja inline.
    

# PERSONAL THOUGHT ==== just my two cents ====
    - Inconsistency .. tabel migration untuk foreign gk di "dropColumn" Foreign column nya
    - Dump SQL != file migration.. -_-'
    - Yang dipake development file migration, lebih nyambung dengan kode
    - bug lumayan banyak, sudah sebagian ditanggulangi

    - print receipt harusnya ada di halaman checkout
    - pada saat checkout barang harusnya otomatis tersimpan data transaksi jika ada perubahan
    - saat menyimpan transaksi, harusnya redirect ke halaman detail transaksi, bukan kembali ke index transaksi
    
