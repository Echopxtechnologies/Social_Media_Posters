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
                                    <i class="fa fa-plug"></i> <?php echo $title; ?>
                                </h4>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo admin_url('sm_posters/add_connection'); ?>" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add Connection
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php if (!empty($connections)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped dataTable">
                                    <thead>
                                        <tr>
                                            <th>Platform</th>
                                            <th>Account Name</th>
                                            <th>Account ID</th>
                                            <th>Client</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $platform_info = [
                                            'facebook' => ['name' => 'Facebook', 'icon' => 'fa-facebook', 'color' => '#4267B2'],
                                            'instagram' => ['name' => 'Instagram', 'icon' => 'fa-instagram', 'color' => '#E4405F'],
                                            'x' => ['name' => 'X', 'icon' => 'fa-twitter', 'color' => '#000000'],
                                            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fa-linkedin', 'color' => '#0077B5'],
                                            'tumblr' => ['name' => 'Tumblr', 'icon' => 'fa-tumblr', 'color' => '#35465C'],
                                            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fa-pinterest', 'color' => '#BD081C']
                                        ];
                                        
                                        foreach ($connections as $conn) { 
                                            $info = isset($platform_info[$conn->platform]) ? $platform_info[$conn->platform] : ['name' => ucfirst($conn->platform), 'icon' => 'fa-share', 'color' => '#999'];
                                        ?>
                                            <tr>
                                                <td>
                                                    <i class="fa <?php echo $info['icon']; ?>" style="color: <?php echo $info['color']; ?>; font-size: 18px; margin-right: 5px;"></i>
                                                    <strong><?php echo $info['name']; ?></strong>
                                                </td>
                                                <td><?php echo $conn->account_name ? $conn->account_name : '<span class="text-muted">-</span>'; ?></td>
                                                <td><code><?php echo $conn->account_id; ?></code></td>
                                                <td><?php echo $conn->company ? $conn->company : '<span class="text-muted">No client</span>'; ?></td>
                                                <td>
                                                    <?php if ($conn->status == 1) { ?>
                                                        <span class="label label-success">Active</span>
                                                    <?php } else { ?>
                                                        <span class="label label-default">Inactive</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo _dt($conn->created_at); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="<?php echo admin_url('sm_posters/edit_connection/' . $conn->id); ?>" 
                                                           class="btn btn-default btn-sm">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('sm_posters/toggle_connection/' . $conn->id); ?>" 
                                                           class="btn btn-default btn-sm"
                                                           title="Toggle Status">
                                                            <i class="fa fa-power-off"></i>
                                                        </a>
                                                        <a href="<?php echo admin_url('sm_posters/delete_connection/' . $conn->id); ?>" 
                                                           class="btn btn-danger btn-sm _delete">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <p><i class="fa fa-plug fa-2x"></i></p>
                                <h4>No Connections Yet</h4>
                                <p>Connect your social media accounts to start posting!</p>
                                <a href="<?php echo admin_url('sm_posters/add_connection'); ?>" class="btn btn-primary btn-lg mtop15">
                                    <i class="fa fa-plus"></i> Add Your First Connection
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($('.dataTable').length) {
        $('.dataTable').DataTable({
            order: [[0, 'asc']]
        });
    }
});
</script>

<?php init_tail(); ?>
</body>
</html>