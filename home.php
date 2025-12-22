<h4>FMMC – Cashier Queuing System</h4>
<hr>

<?php 
    $vid = scandir('./video');
    $video = $vid[2] ?? null;
?>

<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="card shadow-sm">
            <div class="card-body text-center">

                <?php if ($video): ?>
                    <video 
                        src="./video/<?= $video ?>" 
                        class="bg-dark mb-2"
                        style="max-height:50vh; width:100%;"
                        autoplay 
                        muted 
                        loop 
                        controls>
                    </video>
                <?php else: ?>
                    <p class="text-muted">No video uploaded.</p>
                <?php endif; ?>

                <!-- Alert -->
                <div id="alert-box" class="alert d-none"></div>

                <form id="upload-form" enctype="multipart/form-data">
                    <input type="hidden" name="video" value="<?= $video ?>">

                    <div class="row justify-content-center mb-3">
                        <div class="col-md-6 text-start">
                            <label class="form-label">Update Video</label>
                            <input 
                                type="file" 
                                name="vid" 
                                class="form-control" 
                                accept="video/*" 
                                required>
                        </div>
                    </div>

                    <button class="btn btn-success" type="submit">
                        Update Video
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>
<script>
    $(function(){

        $('#upload-form').on('submit', function(e){
            e.preventDefault();

            const form = $(this);
            const btn = form.find('button[type="submit"]');
            const alertBox = $('#alert-box');

            alertBox.addClass('d-none').removeClass('alert-success alert-danger');
            btn.prop('disabled', true).text('Uploading...');

            $.ajax({
                url: './Actions.php?a=update_video',
                method: 'POST',
                data: new FormData(this),
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(resp){
                    if (resp.status === 'success') {
                        alertBox
                            .removeClass('d-none')
                            .addClass('alert-success')
                            .text(resp.msg || 'Video updated successfully.');

                        setTimeout(() => location.reload(), 1200);
                    } else {
                        alertBox
                            .removeClass('d-none')
                            .addClass('alert-danger')
                            .text(resp.msg || 'Upload failed.');
                    }
                    btn.prop('disabled', false).text('Update Video');
                },
                error: function(){
                    alertBox
                        .removeClass('d-none')
                        .addClass('alert-danger')
                        .text('An error occurred.');
                    btn.prop('disabled', false).text('Update Video');
                }
            });
        });

    });
</script>