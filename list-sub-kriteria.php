<?php
require_once('includes/init.php');
cek_login(array(1));

$page = "Sub Kriteria";
require_once('template/header.php');

$errors = array();
$sts = array();

// Handle Add Operation
if(isset($_POST['tambah'])):	
    $id_kriteria = trim($_POST['id_kriteria']);
    $nama = trim($_POST['nama']);
    $nilai = trim($_POST['nilai']);

    if(!$id_kriteria) {
        $errors[] = 'ID kriteria tidak boleh kosong';
    }
    if(!$nama) {
        $errors[] = 'Nama sub kriteria tidak boleh kosong';
    }		
    if(!$nilai) {
        $errors[] = 'Nilai sub kriteria tidak boleh kosong';
    }

    if(empty($errors)):
        // Use prepared statements for security
        $stmt = $koneksi->prepare("INSERT INTO sub_kriteria (id_kriteria, nama, nilai) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $id_kriteria, $nama, $nilai);

        if ($stmt->execute()) {
            $sts[] = 'Data berhasil disimpan';
        } else {
            $errors[] = 'Data gagal disimpan: ' . $stmt->error;
        }

        $stmt->close();
    endif;
endif;

// Handle Edit Operation
if(isset($_POST['edit'])):	
    $id_sub_kriteria = trim($_POST['id_sub_kriteria']);
    $id_kriteria = trim($_POST['id_kriteria']);
    $nama = trim($_POST['nama']);
    $nilai = trim($_POST['nilai']);

    if(!$id_kriteria) {
        $errors[] = 'ID kriteria tidak boleh kosong';
    }
    if(!$nama) {
        $errors[] = 'Nama sub kriteria tidak boleh kosong';
    }		
    if(!$nilai) {
        $errors[] = 'Nilai sub kriteria tidak boleh kosong';
    }

    if(empty($errors)):
        // Use prepared statements for security
        $stmt = $koneksi->prepare("UPDATE sub_kriteria SET nama = ?, nilai = ? WHERE id_kriteria = ? AND id_sub_kriteria = ?");
        $stmt->bind_param("ssii", $nama, $nilai, $id_kriteria, $id_sub_kriteria);

        if ($stmt->execute()) {
            $sts[] = 'Data berhasil diupdate';
        } else {
            $errors[] = 'Data gagal diupdate: ' . $stmt->error;
        }

        $stmt->close();
    endif;
endif;
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-cubes"></i> Data Sub Kriteria</h1>
</div>

<?php if(!empty($sts)): ?>
    <div class="alert alert-info">
        <?php foreach($sts as $st): ?>
            <p><?php echo htmlspecialchars($st, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
            <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = '';
switch($status):
    case 'sukses-baru':
        $msg = 'Data berhasil disimpan';
        break;
    case 'sukses-hapus':
        $msg = 'Data berhasil dihapus';
        break;
    case 'sukses-edit':
        $msg = 'Data berhasil diupdate';
        break;
endswitch;

if($msg):
    echo '<div class="alert alert-info">' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
endif;

$query = $koneksi->query("SELECT * FROM kriteria WHERE ada_pilihan='1' ORDER BY kode_kriteria ASC");
$cek = $query->num_rows;
if($cek <= 0) {
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning"><i class="fa fa-table"></i> Daftar Data Sub Kriteria</h6>
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            Cara penilaian pada kriteria berjenis input langsung semua.
        </div>
    </div>
</div>
<?php
} else {
    while($data = $query->fetch_assoc()) {
?>
<div style="color:black;" class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-warning"><i class="fa fa-table"></i> <?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8') . " (" . htmlspecialchars($data['kode_kriteria'], ENT_QUOTES, 'UTF-8') . ")" ?></h6>
            <a href="#tambah<?= htmlspecialchars($data['id_kriteria'], ENT_QUOTES, 'UTF-8'); ?>" data-toggle="modal" class="btn btn-sm btn-success"> <i class="fa fa-plus"></i> Tambah Data </a>
        </div>
    </div>
    
    <div class="modal fade" id="tambah<?= htmlspecialchars($data['id_kriteria'], ENT_QUOTES, 'UTF-8'); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> Tambah <?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form style="color:black;" action="" method="post">
                    <div class="modal-body">
                        <input style="color:black;" type="text" name="id_kriteria" value="<?= htmlspecialchars($data['id_kriteria'], ENT_QUOTES, 'UTF-8'); ?>" hidden>
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Sub Kriteria</label>
                            <input style="color:black;" autocomplete="off" type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Nilai</label>
                            <input style="color:black;" autocomplete="off" step="0.001" type="number" name="nilai" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
                        <button type="submit" name="tambah" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div style="color:black;" class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="bg-warning text-white">
                    <tr align="center">						
                        <th width="5%">No</th>
                        <th>Nama Sub Kriteria</th>
                        <th>Nilai</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $no = 1;
                        $id_kriteria = $data['id_kriteria'];
                        $q = $koneksi->query("SELECT * FROM sub_kriteria WHERE id_kriteria = '$id_kriteria' ORDER BY nilai DESC");
                        while($d = $q->fetch_assoc()){
                    ?>
                    <tr style="color:black;"  align="center">
                        <td><?= htmlspecialchars($no, ENT_QUOTES, 'UTF-8') ?></td>
                        <td align="left"><?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($d['nilai'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a data-toggle="modal" title="Edit Data" href="#editsk<?= htmlspecialchars($d['id_sub_kriteria'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                                
                                <a data-toggle="tooltip" data-placement="bottom" title="Hapus Data" href="hapus-sub-kriteria.php?id=<?= htmlspecialchars($d['id_sub_kriteria'], ENT_QUOTES, 'UTF-8') ?>" onclick="return confirm('Apakah anda yakin untuk menghapus data ini?')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editsk<?= htmlspecialchars($d['id_sub_kriteria'], ENT_QUOTES, 'UTF-8') ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel"><i class="fa fa-edit"></i> Edit <?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                </div>
                                <form action="" method="post">
                                    <input type="text" name="id_sub_kriteria" value="<?= htmlspecialchars($d['id_sub_kriteria'], ENT_QUOTES, 'UTF-8') ?>" hidden>
                                    <input type="text" name="id_kriteria" value="<?= htmlspecialchars($d['id_kriteria'], ENT_QUOTES, 'UTF-8') ?>" hidden>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Nama Sub Kriteria</label>
                                            <input type="text" autocomplete="off" class="form-control" value="<?= htmlspecialchars($d['nama'], ENT_QUOTES, 'UTF-8') ?>" name="nama" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold">Nilai</label>
                                            <input type="number" step="0.001" autocomplete="off" name="nilai" class="form-control" value="<?= htmlspecialchars($d['nilai'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-times"></i> Batal</button>
                                        <button type="submit" name="edit" class="btn btn-success"><i class="fa fa-save"></i> Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                        $no++;
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
}
}
require_once('template/footer.php');
?>
