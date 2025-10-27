    <div class="sidebar">
        <?php
        if ($_SESSION['jabatan'] == 'admin') {
        ?>
            <h4 class="text-center">Super Admin</h4>
        <?php
        } elseif ($_SESSION['jabatan'] == 'jenjang') {
            $id = $_SESSION['id_user'];
            $query_cari_id = mysqli_query($conn, "SELECT * from jenjang where id_user = '$id'");
            $row_cari_id = mysqli_fetch_array($query_cari_id);
            $id_tingkat = $row_cari_id['id_tingkat'];

            $query_tangkap_tingkat = mysqli_query($conn, "SELECT * from tingkat where id_tingkat = '$id_tingkat'");
            $row_tangkap_tingkat = mysqli_fetch_array($query_tangkap_tingkat);
            $tingkat = $row_tangkap_tingkat['tingkat'];
        ?>
            <h4 class="text-center"> Admin <?php echo $tingkat; ?></h4>
        <?php
        }elseif ($_SESSION['jabatan'] == 'wali_kelas') {
            $idd = $_SESSION['id_user'];
        
            // Cari ID Wali Kelas
            $quer_cari_kelass = mysqli_query($conn, "SELECT * FROM wali_kelas WHERE id_user = '$idd'");
            $row_cari_kelass = mysqli_fetch_array($quer_cari_kelass);
            $id_wali_kelass = $row_cari_kelass['id_wali_kelas'];
        
            // Cari Tahun Ajaran Aktif
            $cari_periodee = mysqli_query($conn, "SELECT * FROM tahun_ajaran WHERE status_aktif = '1'");
            $row_cari_periodee = mysqli_fetch_array($cari_periodee);
            $id_tahun_ajarann = $row_cari_periodee['id_tahun_ajaran'];
        
            // Cari Semua Kelas yang Diampu oleh Wali Kelas
            $quer_walikelass = mysqli_query($conn, "SELECT id_kelas FROM wali_kelas_periode WHERE id_wali_kelas = '$id_wali_kelass' AND id_tahun_ajaran = '$id_tahun_ajarann'");
        
            // Simpan semua ID Kelas dalam array
            $id_kelas_arrayy = [];
            while ($row_wali_kelass = mysqli_fetch_array($quer_walikelass)) {
                $id_kelas_arrayy[] = $row_wali_kelass['id_kelas'];
            }
        
            // Jika ada lebih dari satu kelas, buat format untuk SQL IN
            if (!empty($id_kelas_arrayy)) {
                $id_kelas_listt = "'" . implode("','", $id_kelas_arrayy) . "'";
            } else {
                $id_kelas_listt = "''"; // Default kosong agar tidak error
            }
        
            // Ambil semua kelas berdasarkan ID yang sudah didapat
            $query_tangkap_walii = mysqli_query($conn, "SELECT kelas FROM kelas WHERE id_kelas IN ($id_kelas_listt)");
            
            // Simpan kelas dalam array
            $kelas_array = [];
            while ($row_tangkap_tingkatt = mysqli_fetch_array($query_tangkap_walii)) {
                $kelas_array[] = $row_tangkap_tingkatt['kelas'];
            }
        ?>
            <h4 class="text-center">Wali Kelas <?php echo implode(', ', $kelas_array); ?></h4>
        <?php
        }
        ?>
    
        <a href="data_dashboard.php">Data Dashboard</a>
        <a href="dashboard.php">Data Hari Ini</a>
        
        <a href="data_siswa.php">Data Siswa</a>
        <!-- <a href="data_presensi.php">presensi</a> -->
        
        <a href="data_siswa_kelas.php">siswa dan kelas</a>

        <?php
        if ($_SESSION['jabatan'] == 'admin') {
        ?>
            <a href="hari_libur.php">hari libur</a>
            <a href="data_jenjang.php">Data Jenjang</a>
            <a href="periode.php">Periode</a>
            <a href="kelas.php">kelas</a>
            
        <?php
        }
        ?>
        <?php
        if ($_SESSION['jabatan'] == 'jenjang') {
        ?>
            <a href="kelas.php">kelas</a>
            <a href="wali_kelas.php">wali kelas</a>
            
        <?php
        }
        ?>
        <a href="logout.php">Logout</a>
    </div>