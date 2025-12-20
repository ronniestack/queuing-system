<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <h5 class="mb-0">Users</h5>
        <button class="btn btn-success btn-sm" id="create_new">
            Add New
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:10%">#</th>
                        <th style="width:40%">Full Name</th>
                        <th style="width:30%">Username</th>
                        <th class="text-center" style="width:20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT * FROM user_list WHERE user_id != 1 ORDER BY fullname ASC";
                    $qry = $conn->query($sql);
                    $i = 1;
                    $hasData = false;

                    while ($row = $qry->fetchArray(SQLITE3_ASSOC)):
                        $hasData = true;
                    ?>
                    <tr>
                        <td class="text-center"><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">

                                <!-- EDIT -->
                                <button class="btn btn-outline-primary edit_data"
                                    data-id="<?= $row['user_id'] ?>"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                
                                <!-- DELETE -->
                                <button class="btn btn-outline-danger delete_data"
                                    data-id="<?= $row['user_id'] ?>"
                                    data-name="<?= htmlspecialchars($row['fullname']) ?>"
                                    title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <?php if (!$hasData): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No users found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function(){

        $('#create_new').click(function(){
            uni_modal('Add New User', 'manage_user.php');
        });

        $('.edit_data').click(function(){
            uni_modal(
                'Edit User',
                'manage_user.php?id=' + $(this).data('id')
            );
        });

        $('.delete_data').click(function(){
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Delete User?',
                html: `Are you sure you want to delete <b>${name}</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_data(id);
                }
            });
        });

    });

    function delete_data(id){
        $('#confirm_modal button').attr('disabled', true);

        $.ajax({
            url: './Actions.php?a=delete_user',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(resp){
                if(resp.status === 'success'){
                    location.reload();
                } else {
                    alert('An error occurred.');
                    $('#confirm_modal button').attr('disabled', false);
                }
            },
            error: function(){
                alert('An error occurred.');
                $('#confirm_modal button').attr('disabled', false);
            }
        });
    }
</script>