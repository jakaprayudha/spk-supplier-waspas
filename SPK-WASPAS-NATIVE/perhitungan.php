<?php
require_once('includes/init.php');

$user_role = get_role();
if ($user_role == 'admin') {

$page = "Perhitungan";
require_once('template/header.php');

mysqli_query($koneksi, "TRUNCATE TABLE hasil;");

$kriteria = array();
$q1 = mysqli_query($koneksi, "SELECT * FROM kriteria ORDER BY kode_kriteria ASC");            
while ($krit = mysqli_fetch_array($q1)) {
    $kriteria[$krit['id_kriteria']]['id_kriteria'] = $krit['id_kriteria'];
    $kriteria[$krit['id_kriteria']]['kode_kriteria'] = $krit['kode_kriteria'];
    $kriteria[$krit['id_kriteria']]['nama'] = $krit['nama'];
    $kriteria[$krit['id_kriteria']]['type'] = $krit['type'];
    $kriteria[$krit['id_kriteria']]['bobot'] = $krit['bobot'];
    $kriteria[$krit['id_kriteria']]['ada_pilihan'] = $krit['ada_pilihan'];
}

$alternatif = array();
$q2 = mysqli_query($koneksi, "SELECT * FROM alternatif");            
while ($alt = mysqli_fetch_array($q2)) {
    $alternatif[$alt['id_alternatif']]['id_alternatif'] = $alt['id_alternatif'];
    $alternatif[$alt['id_alternatif']]['nama'] = $alt['nama'];
} 
?>

<!-- ... (HTML bagian sebelumnya tetap sama) ... -->

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning"><i class="fa fa-table"></i> Perhitungan Nilai (Qi)</h6>
    </div>

    <div style="color:black;" class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="bg-warning text-white">
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Nama Alternatif</th>
                        <th width="15%">Nilai Qi</th>
                    </tr>
                </thead>
                <tbody style="color:black;">
                    <?php 
                        $no = 1;
                        foreach ($alternatif as $keys): ?>
                    <tr align="center">
                        <td><?= $no; ?></td>
                        <td align="left"><?= $keys['nama'] ?></td>
                        <?php 
                        $ta = 0;
                        $pa = 1;
                        foreach ($kriteria as $key):
                            $bobot = $key['bobot'];
                            if ($key['ada_pilihan'] == 1) {
                                $q4 = mysqli_query($koneksi, "SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
                                $dt1 = mysqli_fetch_array($q4);
                                
                                $q5 = mysqli_query($koneksi, "SELECT MAX(sub_kriteria.nilai) as max, MIN(sub_kriteria.nilai) as min, kriteria.type FROM penilaian JOIN sub_kriteria ON penilaian.nilai=sub_kriteria.id_sub_kriteria JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria WHERE penilaian.id_kriteria='$key[id_kriteria]'");
                                $dt2 = mysqli_fetch_array($q5);
                                if ($dt2['type'] == "Benefit") {
                                    $nilai_r = $dt1['nilai'] / $dt2['max'];
                                } else {
                                    $nilai_r = $dt2['min'] / $dt1['nilai'];
                                }
                            } else {
                                $q4 = mysqli_query($koneksi, "SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
                                $dt1 = mysqli_fetch_array($q4);
                                
                                $q5 = mysqli_query($koneksi, "SELECT MAX(penilaian.nilai) as max, MIN(penilaian.nilai) as min, kriteria.type FROM penilaian JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria WHERE penilaian.id_kriteria='$key[id_kriteria]'");
                                $dt2 = mysqli_fetch_array($q5);
                                if ($dt2['type'] == "Benefit") {
                                    $nilai_r = $dt1['nilai'] / $dt2['max'];
                                } else {
                                    $nilai_r = $dt2['min'] / $dt1['nilai'];
                                }
                            }
                            $ta += round($nilai_r, 2) * $bobot;
                            $pa *= pow(round($nilai_r, 2), $bobot);
                            $total_hasil = round((0.5 * round($ta, 4)) + (0.5 * round($pa, 4)), 4);
                        endforeach ?>
                        <td>
                            <?php
                                echo $total_hasil;
                                // Menghilangkan kolom id_hasil dari query
                                mysqli_query($koneksi, "INSERT INTO hasil (id_alternatif, nilai) VALUES ('$keys[id_alternatif]', '$total_hasil')");
                            ?>
                        </td>
                    </tr>
                    <?php
                        $no++;
                        endforeach
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once('template/footer.php');
}
else {
    header('Location: login.php');
}
?>