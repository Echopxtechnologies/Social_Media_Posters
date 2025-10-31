<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="no-margin">
                                    <i class="fa fa-history"></i> <?php echo $title; ?>
                                </h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo admin_url('sm_posters/create_post'); ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Create New Post
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php if (!empty($posts)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped dataTable">
                                    <thead>
                                        <tr>
                                            <th>Message</th>
                                            <th>Platforms</th>
                                            <th>Media</th>
                                            <th>Status</th>
                                            <th>Scheduled/Published</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($posts as $post) { 
                                            $platforms = $this->sm_posters_model->get_post_platforms($post->id);
                                            
                                            // Custom character limiter function
                                            $message_preview = $post->message;
                                            if (strlen($message_preview) > 100) {
                                                $message_preview = substr($message_preview, 0, 100) . '...';
                                            }
                                        ?>
                                            <tr>
                                                <td>
                                                    <div style="max-width: 300px;">
                                                        <?php echo nl2br(htmlspecialchars($message_preview)); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($platforms)) { 
                                                        foreach ($platforms as $platform) {
                                                            $icons = [
                                                                'facebook' => ['icon' => 'fa-facebook', 'color' => '#4267B2'],
                                                                'instagram' => ['icon' => 'fa-instagram', 'color' => '#E4405F'],
                                                                'x' => ['icon' => 'fa-twitter', 'color' => '#000000'],
                                                                'linkedin' => ['icon' => 'fa-linkedin', 'color' => '#0077B5'],
                                                                'tumblr' => ['icon' => 'fa-tumblr', 'color' => '#35465C'],
                                                                'pinterest' => ['icon' => 'fa-pinterest', 'color' => '#BD081C']
                                                            ];
                                                            
                                                            $icon = isset($icons[$platform->platform]) ? $icons[$platform->platform] : ['icon' => 'fa-share', 'color' => '#999'];
                                                            
                                                            $status_color = $platform->status == 'published' ? 'success' : ($platform->status == 'failed' ? 'danger' : 'warning');
                                                    ?>
                                                        <span class="label label-<?php echo $status_color; ?>" 
                                                              style="background-color: <?php echo $icon['color']; ?>; margin-right: 5px;"
                                                              title="<?php echo ucfirst($platform->platform) . ': ' . ucfirst($platform->status); ?>">
                                                            <i class="fa <?php echo $icon['icon']; ?>"></i>
                                                        </span>
                                                    <?php } 
                                                    } else { ?>
                                                        <span class="text-muted">None</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($post->media_type == 'image') { ?>
                                                        <i class="fa fa-image text-success"></i> Image
                                                    <?php } elseif ($post->media_type == 'video') { ?>
                                                        <i class="fa fa-video-camera text-info"></i> Video
                                                    <?php } else { ?>
                                                        <i class="fa fa-file-text-o text-muted"></i> Text
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $status_labels = [
                                                        'draft' => 'default',
                                                        'scheduled' => 'info',
                                                        'publishing' => 'warning',
                                                        'published' => 'success',
                                                        'failed' => 'danger'
                                                    ];
                                                    $label_class = isset($status_labels[$post->status]) ? $status_labels[$post->status] : 'default';
                                                    ?>
                                                    <span class="label label-<?php echo $label_class; ?>">
                                                        <?php echo ucfirst($post->status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($post->is_scheduled && $post->status == 'scheduled') { ?>
                                                        <i class="fa fa-clock-o text-info"></i> <?php echo _dt($post->scheduled_at); ?>
                                                    <?php } elseif ($post->published_at) { ?>
                                                        <i class="fa fa-check text-success"></i> <?php echo _dt($post->published_at); ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fa fa-cog"></i> <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu pull-right">
                                                            <li>
                                                                <a href="#" onclick="viewPostDetails(<?php echo $post->id; ?>); return false;">
                                                                    <i class="fa fa-eye"></i> View Details
                                                                </a>
                                                            </li>
                                                            <?php if ($post->status == 'scheduled') { ?>
                                                            <li>
                                                                <a href="<?php echo admin_url('sm_posters/edit_post/' . $post->id); ?>">
                                                                    <i class="fa fa-edit"></i> Edit
                                                                </a>
                                                            </li>
                                                            <?php } ?>
                                                            <li class="divider"></li>
                                                            <li>
                                                                <a href="<?php echo admin_url('sm_posters/delete_post/' . $post->id); ?>" 
                                                                   class="text-danger _delete">
                                                                    <i class="fa fa-trash"></i> Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <p><i class="fa fa-info-circle fa-2x"></i></p>
                                <p>No posts yet. Create your first post!</p>
                                <a href="<?php echo admin_url('sm_posters/create_post'); ?>" class="btn btn-primary mtop15">
                                    <i class="fa fa-plus"></i> Create Post
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Post Details Modal -->
<div class="modal fade" id="postDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Post Details</h4>
            </div>
            <div class="modal-body" id="postDetailsContent">
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewPostDetails(postId) {
    $('#postDetailsModal').modal('show');
    $('#postDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    
    $.get('<?php echo admin_url('sm_posters/get_post_details/'); ?>' + postId, function(response) {
        $('#postDetailsContent').html(response);
    });
}

$(document).ready(function() {
    $('.dataTable').DataTable({
        order: [[4, 'desc']]
    });
});
</script>

<?php init_tail(); ?>
</body>
</html>