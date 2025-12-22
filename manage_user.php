<?php
require_once("DBConnection.php");

if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM user_list WHERE user_id = '{$_GET['id']}'");
    $user = $qry->fetchArray(SQLITE3_ASSOC);
}
?>
<div class="container-fluid">

    <!-- Alert -->
    <div id="alert-box" class="alert d-none" role="alert"></div>

    <form id="user-form">
        <input type="hidden" name="id" value="<?= $user['user_id'] ?? '' ?>">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text"
                   name="fullname"
                   class="form-control"
                   required
                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text"
                   name="username"
                   class="form-control"
                   required
                   value="<?= htmlspecialchars($user['username'] ?? '') ?>">
        </div>
    </form>

</div>
<script>
    $(function(){

        $('#user-form').on('submit', function(e){
            e.preventDefault();

            const form = $(this);
            const alertBox = $('#alert-box');
            const submitBtn = $('#uni_modal button[type="submit"]');

            alertBox.addClass('d-none').removeClass('alert-success alert-danger');
            submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: './Actions.php?a=save_user',
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(resp){
                    if (resp.status === 'success') {
                        alertBox
                            .removeClass('d-none')
                            .addClass('alert-success')
                            .text(resp.msg || 'User saved successfully.');

                        $('#uni_modal').on('hidden.bs.modal', function(){
                            location.reload();
                        });

                        if (!form.find('input[name="id"]').val()) {
                            form[0].reset();
                        }
                    } else {
                        alertBox
                            .removeClass('d-none')
                            .addClass('alert-danger')
                            .text(resp.msg || 'An error occurred.');
                    }

                    submitBtn.prop('disabled', false).text('Save');
                },
                error: function(){
                    alertBox
                        .removeClass('d-none')
                        .addClass('alert-danger')
                        .text('An error occurred.');

                    submitBtn.prop('disabled', false).text('Save');
                }
            });
        });

    });
</script>
