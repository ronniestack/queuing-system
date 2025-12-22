<div class="container">
    <div class="row g-4">

        <!-- NOW SERVING -->
        <div class="col-md-6 d-flex align-items-center justify-content-center" id="serving-field">
            <div class="card shadow-sm w-100" style="max-width:420px">
                <div class="card-header bg-light text-center">
                    <h5 class="mb-0 fw-semibold">Now Serving</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="display-3 fw-bold text-success" id="queue">----</div>
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="col-md-6 d-flex align-items-center justify-content-center" id="action-field">
            <div class="w-100" style="max-width:420px">
                <div class="row g-3">
                    <div class="col-12">
                        <button id="next_queue"
                                class="btn btn-success btn-lg w-100">
                            <i class="fa-solid fa-forward me-2"></i>
                            Next Queue
                        </button>
                    </div>
                    <div class="col-12">
                        <button id="notify"
                                class="btn btn-outline-secondary btn-lg w-100">
                            <i class="fa-solid fa-bullhorn me-2"></i>
                            Notify
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    var websocket = new WebSocket("ws://127.0.0.1:2306");

    websocket.onopen = function () {
        console.log('socket is open!');
    };

    websocket.onclose = function () {
        console.log('socket has been closed!');
        // optional auto-reconnect
        setTimeout(() => {
            websocket = new WebSocket("ws://127.0.0.1:2306");
        }, 1000);
    };

    var in_queue = {};

    function _resize_elements(){
        var window_height = $(window).height();
        var nav_height = $('#topNavBar').height();
        $('#serving-field,#action-field').height(window_height - nav_height - 50);
    }

    function get_queue(){
        $.ajax({
            url:'./../Actions.php?a=next_queue',
            dataType:'json',
            error: err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching the queue.'
                });
            },
            success:function(resp){
                if(resp.status){
                    if(Object.keys(resp.data).length > 0){
                        in_queue = resp.data;
                        queue();
                    }else{
                        in_queue = {};
                        Swal.fire({
                            icon: 'info',
                            title: 'No Queue',
                            text: 'No queue available at the moment.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        queue();
                    }
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred.'
                    });
                }
            }
        });
    }

    function queue(){
        $('#queue').text(in_queue.queue || "----");
        if(in_queue.queue){
            websocket.send(JSON.stringify({
                type: 'queue',
                cashier_id: '<?php echo $_SESSION['cashier_id'] ?>',
                qid: in_queue.queue_id
            }));
        }
    }

    _resize_elements();

    $(function(){
        $(window).resize(_resize_elements);

        $('#next_queue').click(function(){
            get_queue();
        });

        $('#notify').click(function(){
            if(!!in_queue.queue){
                queue();
            }else{
                Swal.fire({
                    icon: 'info',
                    title: 'No Queue',
                    text: 'No queue available to notify.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
</script>