<?php
session_start();

//Koneksi ke Database
$conn = mysqli_connect("localhost", "root", "", "inventory-web");

//Menambah barang baru stock
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    //UPLOAD GAMBAR
    $allowed_extension = array('png', 'jpg');
    $nama = $_FILES['file']['name']; //NGAMBIL NAMA GAMBAR
    $dot = explode('.', $nama);
    $ekstensi = strtolower(end($dot)); // NGAMBIL EKSTENSINYA
    $ukuran = $_FILES['file']['size']; // NGAMBIL SIZE FILENYA
    $file_tmp = $_FILES['file']['tmp_name']; // NGAMBIL LOKASI FILENYA

    //PENAMAAN FILE -> ENKRIPSI
    $image = md5(uniqid($nama, true) . time()) . '.' . $ekstensi; //MENGGABUNGKAN NAMA FILE YANG DI ENKRIPSI DGN EKSTENSINYA

    //VALIDASI UDAH ADA ATAU BELUM
    $cek = mysqli_query($conn, "SELECT * FROM stock WHERE nama_barang='$namabarang'");
    $hitung = mysqli_num_rows($cek);

    if ($hitung < 1) {
        //JIKA BELUM ADA

        //PROSES UPLOAD GAMBAR
        if (in_array($ekstensi, $allowed_extension) === true) {
            //VALIDASI UKURAN FILE
            if ($ukuran < 1500000) {
                move_uploaded_file($file_tmp, 'images/' . $image);

                $addtotable = mysqli_query($conn, "INSERT INTO stock (nama_barang, deskripsi, stock, image) VALUES('$namabarang','$deskripsi', '$stock', '$image')");
                if ($addtotable) {
                    header('location:index.php');
                } else {
                    echo 'Gagal';
                    header('location:index.php');
                }
            } else {
                //KALAU FILE LEBIH dari 1.5MB
                echo '
            <script>
            alert("Ukuran File Terlalu Besar");
            windows.location.href="index.php";
            </script>
            ';
            }
        } else {
            //KALAU GAMBAR NYA TIDAK JPG/PNG
            echo '
            <script>
            alert("Format File Tidak PNG/JPG");
            windows.location.href="index.php";
            </script>
            ';
        }

    } else {
        // JIKA SUDAH ADA
        echo '
        <script>
        alert("Nama Barang Sudah Terdaftar");
        windows.location.href="index.php";
        </script>
        ';
    }
}
;

//Menambah barang masuk
if (isset($_POST['barangmasuk'])) {
    $barangnya = $_POST['barangnya'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $cekstockbarang = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstockbarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $qty;



    $addtomasuk = mysqli_query($conn, "INSERT INTO masuk (id_barang, keterangan, qty) VALUES('$barangnya', '$keterangan', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE id_barang='$barangnya'");
    if ($addtomasuk && $updatestockmasuk) {
        header('location:masuk.php');
    } else {
        echo 'Gagal';
        header('location:masuk.php');

    }
}
;

//Menambah barang keluar
if (isset($_POST['addbarangkeluar'])) {
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $cekstockbarang = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstockbarang);

    $stocksekarang = $ambildatanya['stock'];

    if ($stocksekarang >= $qty) {
        // KALAU BARANG STOCK NYA CUKUP
        $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;

        $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (id_barang, penerima, qty) VALUES('$barangnya', '$penerima', '$qty')");
        $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE id_barang='$barangnya'");
        if ($addtokeluar && $updatestockmasuk) {
            header('location:keluar.php');
        } else {
            echo 'Gagal';
            header('location:keluar.php');

        }
    } else {
        // KALAU BARANG STOCK YANG DI KELUARKAN GA CUKUP
        echo '
        <script>
            alert("Stock Saat Ini Tidak Mencukupi");
            window.location.href="keluar.php";
        </script>
        ';
    }
}


// UPDATE BARANG DARI STOCK
if (isset($_POST['updatebarang'])) {
    $idb = $_POST['idb'];
    $deskripsi = $_POST['deskripsi'];
    $namabarang = $_POST['namabarang'];

    //UPLOAD GAMBAR
    $allowed_extension = array('png', 'jpg');
    $nama = $_FILES['file']['name']; //NGAMBIL NAMA GAMBAR
    $dot = explode('.', $nama);
    $ekstensi = strtolower(end($dot)); // NGAMBIL EKSTENSINYA
    $ukuran = $_FILES['file']['size']; // NGAMBIL SIZE FILENYA
    $file_tmp = $_FILES['file']['tmp_name']; // NGAMBIL LOKASI FILENYA

    //PENAMAAN FILE -> ENKRIPSI
    $image = md5(uniqid($nama, true) . time()) . '.' . $ekstensi; //MENGGABUNGKAN NAMA FILE YANG DI ENKRIPSI DGN EKSTENSINYA

    if ($ukuran == 0) {
        // JIKA TIDAK INGIN UPLOAD
        $update = mysqli_query($conn, "UPDATE stock SET nama_barang='$namabarang', deskripsi='$deskripsi' WHERE id_barang='$idb'");
        if ($update) {
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
        }
    } else {
        //JIKA INGIN UPLOAD
        move_uploaded_file($file_tmp, 'images/' . $image);
        $update = mysqli_query($conn, "UPDATE stock SET nama_barang='$namabarang', deskripsi='$deskripsi', image='$image' WHERE id_barang='$idb'");
        if ($update) {
            header('location:index.php');
        } else {
            echo 'Gagal';
            header('location:index.php');
        }
    }
}

//MENGHAPUS BARANG DARI STOCK
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb']; // id Barang

    $gambar = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$idb'");
    $get = mysqli_fetch_array($gambar);
    $img = 'images/' . $get['image'];
    unlink($img);

    $hapus = mysqli_query($conn, "DELETE FROM stock WHERE id_barang='$idb'");
    if ($hapus) {
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    }
}

// MENGUBAH DATA BARANG MASUK
if (isset($_POST['updatebarangmasuk'])) {
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $deskripsi = $_POST['keterangan'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrng = $stocknya['stock'];

    $qtyskrng = mysqli_query($conn, "SELECT * FROM masuk WHERE id_masuk='$idm'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtyskrng = $qtynya['qty'];

    if ($qty > $qtyskrng) {
        $selisih = $qty - $qtyskrng;
        $kurangin = $stockskrng + $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE id_barang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE masuk set qty='$qty', keterangan='$deskripsi' WHERE id_masuk='$idm'");
        if ($kurangistocknya && $updatenya) {
            header('location:masuk.php');
        } else {
            echo 'Gagal';
            header('location:masuk.php');
        }
    } else {
        $selisih = $qtyskrng - $qty;
        $kurangin = $stockskrng - $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE id_barang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE masuk set qty='$qty', keterangan='$deskripsi' WHERE id_masuk='$idm'");
        if ($kurangistocknya && $updatenya) {
            header('location:masuk.php');
        } else {
            echo 'Gagal';
            header('location:masuk.php');
        }
    }
}

// MENGHAPUS BARANG MASUK
if (isset($_POST['hapusbarangmasuk'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok - $qty;

    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE id_barang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM masuk WHERE id_masuk='$idm'");

    if ($update && $hapusdata) {
        header('location:masuk.php');
    } else {
        header('location:masuk.php');
    }
}

// MENGUBAH DATA BARANG KELUAR
if (isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $qty = $_POST['qty'];

    $lihatstock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrng = $stocknya['stock'];

    $qtyskrng = mysqli_query($conn, "SELECT * FROM keluar WHERE id_keluar='$idk'");
    $qtynya = mysqli_fetch_array($qtyskrng);
    $qtyskrng = $qtynya['qty'];

    if ($qty > $qtyskrng) {
        $selisih = $qty - $qtyskrng;
        $kurangin = $stockskrng - $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE id_barang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE keluar set qty='$qty', penerima='$penerima' WHERE id_keluar='$idk'");
        if ($kurangistocknya && $updatenya) {
            header('location:keluar.php');
        } else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    } else {
        $selisih = $qtyskrng - $qty;
        $kurangin = $stockskrng + $selisih;
        $kurangistocknya = mysqli_query($conn, "UPDATE stock SET stock='$kurangin' WHERE id_barang='$idb'");
        $updatenya = mysqli_query($conn, "UPDATE keluar set qty='$qty', penerima='$penerima' WHERE id_keluar='$idk'");
        if ($kurangistocknya && $updatenya) {
            header('location:keluar.php');
        } else {
            echo 'Gagal';
            header('location:keluar.php');
        }
    }
}

// MENGHAPUS BARANG KELUAR
if (isset($_POST['hapusbarangkeluar'])) {
    $idb = $_POST['idb'];
    $qty = $_POST['qty'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($conn, "SELECT * FROM stock WHERE id_barang='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stok = $data['stock'];

    $selisih = $stok + $qty;

    $update = mysqli_query($conn, "UPDATE stock SET stock='$selisih' WHERE id_barang='$idb'");
    $hapusdata = mysqli_query($conn, "DELETE FROM keluar WHERE id_keluar='$idk'");

    if ($update && $hapusdata) {
        header('location:keluar.php');
    } else {
        header('location:keluar.php');
    }
}

// MENAMBAH AKUN BARU ADMIN
if (isset($_POST['addadmin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $queryinsert = mysqli_query($conn, "INSERT INTO login (email, password) VALUES ('$email','$password')");

    if ($queryinsert) {
        // JIKA BERHASIL
        header('location:admin.php');
    } else {
        // JIKA GAGAL
        header('location:admin.php');
    }
}

// EDIT DATA AKUN ADMIN
if (isset($_POST['updateadmin'])) {
    $emailbaru = $_POST['emailadmin'];
    $passwordbaru = $_POST['passwordbaru'];
    $idnya = $_POST['id'];

    $queryupdate = mysqli_query($conn, "UPDATE login SET email='$emailbaru', password='$passwordbaru' WHERE iduser='$idnya'");

    if ($queryupdate) {
        header('location:admin.php');

    } else {
        header('location:admin.php');

    }
}

// HAPUS DATA AKUN ADMIN
if (isset($_POST['hapusadmin'])) {
    $id = $_POST['id'];

    $querydelete = mysqli_query($conn, "DELETE FROM login WHERE iduser='$id'");

    if ($querydelete) {
        header('location:admin.php');

    } else {
        header('location:admin.php');

    }
}

?>