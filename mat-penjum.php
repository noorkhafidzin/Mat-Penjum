<?php
include 'mat-penjum-dbconn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Game MatPenjum</title>
</head>

<body>
    <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Game MatPenjum</span>
        </div>
    </nav>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <?php
                if (!isset($_COOKIE['username'])) { ?>
                    <h2>Selamat datang di Game MatPenjum</h2>
                    <p>Ini adalah game matematika sederhana. Untuk mengetahui aturan main klik tombol dibawah ini.</p>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Aturan Main
                    </button>
                    <p></p>
                    <form action="mat-penjum.php" method="POST">
                        Silahkan Masukkan nama Anda : <br><input type="text" name="username" class="form-control mb-1">
                        <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                    </form>
                    <?php
                } else {
                    echo "<p class='text-center'>Selamat datang, <span class='text-success fw-bolder'>" . $_COOKIE['username'] . "</span></p>";
                    echo "<p class='text-center'>Lives : <span class='text-danger'>" . $_COOKIE['lives'] . "</span></p>";
                    echo "<p class='text-center'>Score : <span class='text-primary'>" . $_COOKIE['score'] . "</span></p>";
                    $angka1 = rand(1, 10);
                    $angka2 = rand(1, 10);
                    $res = $angka1 + $angka2;
                    setcookie('result', $res, time() + 3600);
                    echo "<h4 class='text-center'>Pertanyaan:</h4>";
                    echo "<h4 class='text-center'>" . $angka1 . " + " . $angka2 . " = ?</h4>";
                    if (isset($_COOKIE['benar'])) {
                        echo "<p class='text-success text-center'>Jawaban anda benar</p>";
                    }
                    if (isset($_COOKIE['salah'])) {
                        echo "<p class='text-danger text-center'>Jawaban anda salah</p>";
                    }
                    if ($_COOKIE['lives'] > 0) {
                        if (isset($_POST['submit2'])) {
                            $jawab = $_POST['jawab'];
                            if ($jawab == $_COOKIE['result']) {
                                $score = $_COOKIE['score'];
                                $score += 10;
                                setcookie('score', $score, time() + 3600);
                                setcookie('benar', true, time() + 1);
                                header("location: mat-penjum.php");
                            } else {
                                $lives = $_COOKIE['lives'];
                                $lives--;
                                setcookie('lives', $lives, time() + 3600);
                                $score = $_COOKIE['score'];
                                $score -= 2;
                                setcookie('score', $score, time() + 3600);
                                setcookie('salah', true, time() + 1);
                                header("location: mat-penjum.php");
                            }
                        }
                    ?>
                        <form action="mat-penjum.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Masukkan jawabanmu :</label><input type="number" name="jawab" class="form-control" autofocus="autofocus">
                            </div>
                            <div class="text-center">
                                <input type="submit" name="submit2" class="btn btn-primary">
                            </div>
                        </form>
                <?php
                    } else {
                        echo "<h2 class='text-danger'>Game Over!</h2>";
                        echo "Score :" . $_COOKIE['score'];
                        echo "<br>";
                        $tgl = date("Y-m-d H:i:s");
                        if (!isset($_GET['hof'])) {
                            $query = "INSERT INTO hasil (Nama, Score, tgl) VALUES ('" . $_COOKIE['username'] . "', '" . $_COOKIE['score'] . "' , '$tgl')";

                            if (mysqli_query($conn, $query)) {
                                echo "Data permainanmu telah masuk ke database!";
                            } else {
                                echo "Error: " . $query . "<br>" . mysqli_error($conn);
                            }
                        }
                        echo "<br>";
                        echo '<a href="?main-lagi">Main Lagi</a>' . ' | ';
                        echo '<a href="?hof">Hall of Fame</a>';
                        echo "<br>";
                        if (isset($_GET['main-lagi'])) {
                            $nambah = 3;
                            setcookie('lives', $nambah, time() + 3600);
                            $resetscore = 0;
                            setcookie('score', $resetscore, time() + 3600);
                            header('Location: mat-penjum.php');
                            exit;
                        }
                        if (isset($_GET['hof'])) {
                            echo "<h2>Hall of Fame</h2>";
                            echo "<table class='table table-striped table-hover'><tr><th>Peringkat</th><th>ID</th><th>Nama</th><th>Score</th><th>Waktu Main</th></tr>";
                            $query = "select * from hasil ORDER BY Score DESC LIMIT 10";
                            $res = mysqli_query($conn, $query);
                            $peringkat = 1;
                            while ($data = mysqli_fetch_array($res)) {
                                echo "<tr><td>" . $peringkat . "</td><td>" . $data['ID'] . "</td><td>" . $data['Nama'] . "</td><td>" . $data['Score'] . "</td><td>" . $data['tgl'] . "</td></tr>";
                                $peringkat++;
                            }
                        }
                    }
                }
                if (isset($_POST['submit'])) {
                    $nama = $_POST['username'];
                    setcookie('username', $nama, time() + 3600);
                    $lives = 3;
                    setcookie('lives', $lives, time() + 3600);
                    $score = 0;
                    setcookie('score', $score, time() + 3600);
                    header("location: mat-penjum.php");
                }
                ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Aturan Main</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>Ini adalah game matematika penjumlahan 2 angka (1-10).</li>
                        <li>Game dimulai dengan 3 lives(nyawa)</li>
                        <li>Jika jawaban benar akan mendapatkan score sebanyak <span class="text-success"> 10 point</span>.</li>
                        <li>Jika jawaban salah akan mengurangi score sebanyak <span class="text-danger"> 2 point</span> dan mengurangi lives sebanyak <span class="text-danger">1 live</span>.</li>
                        <li>Jika Game Over, maka data Anda akan tersimpan di database.</li>
                        <li>Anda dapat melihat Hall of Fame dari pemain dalam database.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed-bottom" style="z-index:-1;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#e3f2fd" fill-opacity="1" d="M0,224L40,224C80,224,160,224,240,234.7C320,245,400,267,480,250.7C560,235,640,181,720,186.7C800,192,880,256,960,272C1040,288,1120,256,1200,229.3C1280,203,1360,181,1400,170.7L1440,160L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z"></path>
        </svg></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>