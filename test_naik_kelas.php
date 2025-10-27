<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenaikan Kelas Siswa</title>
</head>
<body>

    <h2>Kenaikan Kelas Siswa</h2>
    <form action="proses_kenaikan.php" method="post">
        <label for="kelas_asal">Kelas Asal:</label>
        <select id="kelas_asal" name="kelas_asal" onchange="getSiswa(this.value)">
            <option value="">Pilih Kelas</option>
            <option value="10A">10A</option>
            <option value="10B">10B</option>
            <option value="10C">10C</option>
        </select>

        <br><br>

        <label for="siswa">Pilih Siswa:</label>
        <select id="siswa" name="siswa">
            <option value="">Pilih Siswa</option>
        </select>

        <br><br>

        <label for="kelas_tujuan">Kelas Tujuan:</label>
        <select id="kelas_tujuan" name="kelas_tujuan">
            <option value="">Pilih Kelas</option>
            <option value="11A">11A</option>
            <option value="11B">11B</option>
            <option value="11C">11C</option>
        </select>

        <br><br>

        <button type="submit">Simpan</button>
    </form>

    <script>
        function getSiswa(kelas) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_siswa.php?kelas=" + kelas, true);
            xhr.onload = function () {
                if (this.status == 200) {
                    document.getElementById("siswa").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }
    </script>

</body>
</html>
