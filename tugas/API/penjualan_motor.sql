-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Okt 2022 pada 17.48
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `penjualan_motor`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `motor`
--

CREATE TABLE `motor` (
  `id_motor` int(100) NOT NULL,
  `merek` varchar(100) NOT NULL,
  `harga` varchar(100) NOT NULL,
  `jenis_motor` varchar(100) NOT NULL,
  `generasi` varchar(100) NOT NULL,
  `warna` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `motor`
--

INSERT INTO `motor` (`id_motor`, `merek`, `harga`, `jenis_motor`, `generasi`, `warna`) VALUES
(1, 'honda beat FI-CBS', 'Rp10.099.999', 'matic', '2022', 'silver'),
(2, 'honda beat FI-CBS', 'Rp8.999.999', 'matic', '2020', 'hitam'),
(3, 'honda vario techno', 'Rp12.000.000', 'matic', '2021', 'putih'),
(4, 'honda beat street', 'Rp18.000.999', 'matic', '2022', 'silver'),
(5, 'honda supra GTR 150', 'Rp24.800.999', 'manual kopling', '2021', 'hitam'),
(6, 'honda revo x', 'Rp15.000.000', 'manual', '2021', 'merah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembeli`
--

CREATE TABLE `pembeli` (
  `id_pembeli` int(100) NOT NULL,
  `id_trans` int(100) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `tgl_trans` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pembeli`
--

INSERT INTO `pembeli` (`id_pembeli`, `id_trans`, `nama_pembeli`, `tgl_trans`) VALUES
(1, 1, 'Amin Suparman', '2022-04-01'),
(2, 2, 'azis hidayat', '2022-04-05'),
(3, 3, 'nuraini hidayati', '2022-05-02'),
(3, 4, 'nuraini hidayati', '2022-06-01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok`
--

CREATE TABLE `stok` (
  `id_motor` int(100) NOT NULL,
  `stok_motor` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `stok`
--

INSERT INTO `stok` (`id_motor`, `stok_motor`) VALUES
(1, 88),
(2, 60),
(3, 59),
(4, 39),
(5, 101),
(6, 67);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_trans` int(100) NOT NULL,
  `id_motor` int(100) NOT NULL,
  `jumlah_unit` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id_trans`, `id_motor`, `jumlah_unit`) VALUES
(1, 2, 2),
(2, 2, 5),
(3, 2, 3),
(4, 5, 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `motor`
--
ALTER TABLE `motor`
  ADD PRIMARY KEY (`id_motor`);

--
-- Indeks untuk tabel `pembeli`
--
ALTER TABLE `pembeli`
  ADD PRIMARY KEY (`id_pembeli`,`id_trans`);

--
-- Indeks untuk tabel `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`id_motor`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_trans`,`id_motor`),
  ADD KEY `id_motor` (`id_motor`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `motor`
--
ALTER TABLE `motor`
  MODIFY `id_motor` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_trans` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
