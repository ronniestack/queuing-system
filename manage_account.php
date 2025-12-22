<?php
require_once("DBConnection.php");

$qry = $conn->query("SELECT * FROM user_list WHERE user_id = '{$_SESSION['user_id']}'");
$user = $qry->fetchArray(SQLITE3_ASSOC);
?>

<h4 class="mb-3">Manage Account</h4>
<hr>

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card shadow-sm">
            <div class="card-body">

                <form id="user-form">
                    <input type="hidden" name="id" value="<?= $user['user_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text"
                               name="fullname"
                               class="form-control"
                               required
                               value="<?= htmlspecialchars($user['fullname']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text"
                               name="username"
                               class="form-control"
                               required
                               value="<?= htmlspecialchars($user['username']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password"
                               name="old_password"
                               id="current_password"
                               class="form-control"
                               placeholder="Required to change password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password"
                               name="password"
                               id="new_password"
                               class="form-control"
                               placeholder="Leave blank to keep current password">
                    </div>

                    <!-- Show password toggle -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="togglePassword">
                        <label class="form-check-label" for="togglePassword">
                            Show passwords
                        </label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success" type="submit">
                            Update Account
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
<script>
    $(function(){

        $('#togglePassword').on('change', function(){
            const type = this.checked ? 'text' : 'password';
            $('#new_password, #current_password').attr('type', type);
        });

        $('#user-form').on('submit', function(e){
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: './Actions.php?a=update_credentials',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(resp){
                    // toast already handled globally via flashdata
                    if (resp.status === 'success') {
                        setTimeout(() => location.reload(), 800);
                    } else {
                        submitBtn.prop('disabled', false).text('Update Account');
                    }
                },
                error: function(){
                    submitBtn.prop('disabled', false).text('Update Account');
                }
            });
        });

    });
</script>
