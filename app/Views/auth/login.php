<?= $this->extend('layouts/auth') ?>

<?= $this->section('styles') ?>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color:rgb(223, 228, 232);
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: none;
            background-image: url('https://images.unsplash.com/photo-1557683316-973673baf926');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .card-header {
            background-color:rgb(8, 138, 4);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            border: none;
        }
        .btn-login {
            background-color:rgb(8, 138, 4);
            color: white;
            font-weight: 500;
            padding: 10px;
            border-radius: 5px;
        }
        .input-group-text {
            background-color:rgb(223, 228, 232);
            border-right: none;
            color:rgb(223, 228, 232);
        }
        .form-control {
            border-left: none;
        }
        .input-group {
            margin-bottom: 20px;
        }
        .divider {
            border-top: 1px solidrgb(43, 83, 123);
            margin: 15px 0;
        }
        .signup-link {
            color:rgb(223, 228, 232);
            text-decoration: none;
        }
        .forgot-link {
            color:rgb(223, 228, 232);
            text-decoration: none;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                LOGIN
            </div>
            <div class="card-body p-4">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mb-3" role="alert">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <form action="<?= base_url('login') ?>" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="username" placeholder="Masukkan username" name="username" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control" id="password" placeholder="Masukkan password" name="password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-login">Login</button>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>