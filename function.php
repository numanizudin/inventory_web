<?php
session_start();

//Koneksi ke Database
$conn = mysqli_connect("localhost", "root", "", "inventory-web");

//Menambah barang baru
if (isset($_POST['addnewbarang'])) {
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($conn, "INSERT INTO stock (nama_barang, deskripsi, stock) VALUES('$namabarang','$deskripsi', '$stock')");
    if ($addtotable) {
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');

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
    $tambahkanstocksekarangdenganquantity = $stocksekarang - $qty;



    $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (id_barang, penerima, qty) VALUES('$barangnya', '$penerima', '$qty')");
    $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE id_barang='$barangnya'");
    if ($addtokeluar && $updatestockmasuk) {
        header('location:keluar.php');
    } else {
        echo 'Gagal';
        header('location:keluar.php');

    }
}

?>