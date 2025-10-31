<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-plug"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

                        <?php echo form_open(current_url()); ?>

                        <!-- Client Selection -->
                        <div class="form-group">
                            <label for="client_id" class="control-label">
                                <span class="text-danger">*</span> Client
                            </label>
                            <select name="client_id" id="client_id" class="selectpicker form-control" 
                                    data-live-search="true" required>
                                <option value="">-- Select Client --</option>
                                <?php foreach ($clients as $client) { ?>
                                    <option value="<?php echo $client['userid']; ?>" 
                                            <?php echo ($connection && $connection->client_id == $client['userid']) ? 'selected' : ''; ?>>
                                        <?php echo $client['company']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Platform Selection -->
                        <div class="form-group">
                            <label for="platform" class="control-label">
                                <span class="text-danger">*</span> Platform
                            </label>
                            <select name="platform" id="platform" class="form-control" required 
                                    <?php echo $connection ? 'disabled' : ''; ?>>
                                <option value="">-- Select Platform --</option>
                                <option value="facebook" <?php echo ($connection && $connection->platform == 'facebook') ? 'selected' : ''; ?>>
                                    Facebook
                                </option>
                                <option value="instagram" <?php echo ($connection && $connection->platform == 'instagram') ? 'selected' : ''; ?>>
                                    Instagram
                                </option>
                                <option value="x" <?php echo ($connection && $connection->platform == 'x') ? 'selected' : ''; ?>>
                                    X (Twitter)
                                </option>
                                <option value="linkedin" <?php echo ($connection && $connection->platform == 'linkedin') ? 'selected' : ''; ?>>
                                    LinkedIn
                                </option>
                                <option value="tumblr" <?php echo ($connection && $connection->platform == 'tumblr') ? 'selected' : ''; ?>>
                                    Tumblr
                                </option>
                                <option value="pinterest" <?php echo ($connection && $connection->platform == 'pinterest') ? 'selected' : ''; ?>>
                                    Pinterest
                                </option>
                            </select>
                            <?php if ($connection) { ?>
                                <input type="hidden" name="platform" value="<?php echo $connection->platform; ?>">
                            <?php } ?>
                        </div>

                        <!-- Account Name -->
                        <div class="form-group">
                            <label for="account_name" class="control-label">
                                Account Name (Optional)
                            </label>
                            <input type="text" 
                                   name="account_name" 
                                   id="account_name" 
                                   class="form-control" 
                                   placeholder="e.g., My Business Page"
                                   value="<?php echo $connection ? $connection->account_name : set_value('account_name'); ?>">
                            <p class="text-muted"><small>A friendly name to identify this account</small></p>
                        </div>

                        <!-- Account ID -->
                        <div class="form-group">
                            <label for="account_id" class="control-label">
                                <span class="text-danger">*</span> Account ID
                            </label>
                            <input type="text" 
                                   name="account_id" 
                                   id="account_id" 
                                   class="form-control" 
                                   placeholder="Page ID / Account ID / Board ID"
                                   value="<?php echo $connection ? $connection->account_id : set_value('account_id'); ?>"
                                   required>
                            <p class="text-muted">
                                <small id="account_id_help">
                                    <strong>Facebook:</strong> Page ID<br>
                                    <strong>Instagram:</strong> Instagram Business Account ID<br>
                                    <strong>X:</strong> Username or User ID<br>
                                    <strong>LinkedIn:</strong> Person/Organization URN<br>
                                    <strong>Tumblr:</strong> Blog hostname<br>
                                    <strong>Pinterest:</strong> Board ID
                                </small>
                            </p>
                        </div>

                        <!-- Access Token -->
                        <div class="form-group">
                            <label for="access_token" class="control-label">
                                <span class="text-danger">*</span> Access Token
                            </label>
                            <textarea name="access_token" 
                                      id="access_token" 
                                      rows="4" 
                                      class="form-control" 
                                      placeholder="Paste your access token here"
                                      required><?php echo $connection ? $connection->access_token : set_value('access_token'); ?></textarea>
                            <p class="text-muted"><small>Long-lived access token for API authentication</small></p>
                        </div>

                        <!-- Refresh Token (Optional) -->
                        <div class="form-group">
                            <label for="refresh_token" class="control-label">
                                Refresh Token (Optional)
                            </label>
                            <textarea name="refresh_token" 
                                      id="refresh_token" 
                                      rows="3" 
                                      class="form-control" 
                                      placeholder="Refresh token (if applicable)"><?php echo $connection ? $connection->refresh_token : set_value('refresh_token'); ?></textarea>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" 
                                           name="status" 
                                           value="1" 
                                           <?php echo (!$connection || $connection->status == 1) ? 'checked' : ''; ?>>
                                    Active
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Action Buttons -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> <?php echo $connection ? 'Update Connection' : 'Add Connection'; ?>
                        </button>
                        <a href="<?php echo admin_url('sm_posters/connections'); ?>" class="btn btn-default">
                            Cancel
                        </a>

                        <?php echo form_close(); ?>

                    </div>
                </div>

                <!-- Help Panel -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><i class="fa fa-question-circle"></i> How to Get Access Tokens</h4>
                        <hr>
                        
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#facebook-help">
                                            <i class="fa fa-facebook"></i> Facebook
                                        </a>
                                    </h4>
                                </div>
                                <div id="facebook-help" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol>
                                            <li>Go to <a href="https://developers.facebook.com" target="_blank">Facebook Developers</a></li>
                                            <li>Create an app or use existing one</li>
                                            <li>Generate Page Access Token with <code>pages_manage_posts</code> permission</li>
                                            <li>Exchange for long-lived token (60 days)</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#instagram-help">
                                            <i class="fa fa-instagram"></i> Instagram
                                        </a>
                                    </h4>
                                </div>
                                <div id="instagram-help" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <p>Instagram requires a Business Account connected to a Facebook Page.</p>
                                        <ol>
                                            <li>Convert to Business Account in Instagram settings</li>
                                            <li>Connect to Facebook Page</li>
                                            <li>Use Facebook's Graph API to get Instagram Business Account ID</li>
                                            <li>Use the same Facebook Page Access Token</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#x-help">
                                            <i class="fa fa-twitter"></i> X (Twitter)
                                        </a>
                                    </h4>
                                </div>
                                <div id="x-help" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol>
                                            <li>Go to <a href="https://developer.twitter.com" target="_blank">Twitter Developer Portal</a></li>
                                            <li>Create app with Read & Write permissions</li>
                                            <li>Generate Bearer Token</li>
                                            <li>Note: Requires Elevated access for posting</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#linkedin-help">
                                            <i class="fa fa-linkedin"></i> LinkedIn
                                        </a>
                                    </h4>
                                </div>
                                <div id="linkedin-help" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <ol>
                                            <li>Create app on <a href="https://www.linkedin.com/developers/" target="_blank">LinkedIn Developers</a></li>
                                            <li>Request <code>w_member_social</code> scope</li>
                                            <li>Implement OAuth 2.0 flow</li>
                                            <li>Get User Access Token</li>
                                        </ol>
                                    </div>
                                </div>

                                <div id="facebook-help" class="panel-collapse collapse">
    <div class="panel-body">
        <h5><strong>Quick Setup (For Testing):</strong></h5>
        <ol>
            <li>Go to <a href="https://developers.facebook.com/tools/explorer/" target="_blank">Graph API Explorer</a></li>
            <li>Select your App (or create one at <a href="https://developers.facebook.com/apps/" target="_blank">developers.facebook.com/apps</a>)</li>
            <li>Click "Generate Access Token" with these permissions:
                <ul>
                    <li><code>pages_show_list</code></li>
                    <li><code>pages_read_engagement</code></li>
                    <li><code>pages_manage_posts</code></li>
                    <li><code>pages_manage_engagement</code></li>
                </ul>
            </li>
            <li>Run this query: <code>GET /me/accounts</code></li>
            <li>Copy the <strong>Page ID</strong> and <strong>Page Access Token</strong></li>
            <li>Paste them below</li>
        </ol>
        
        <div class="alert alert-warning">
            <strong>Important:</strong>
            <ul>
                <li>User tokens expire in 1-2 hours</li>
                <li>Page tokens from Graph Explorer expire in 1-2 hours</li>
                <li>For production, exchange for long-lived token (60 days)</li>
                <li>Your Facebook account must be Admin/Editor of the Page</li>
            </ul>
        </div>

        <h5><strong>Get Long-Lived Token (60 days):</strong></h5>
        <p>In Graph API Explorer, run:</p>
        <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px;">GET /oauth/access_token?grant_type=fb_exchange_token&client_id=YOUR_APP_ID&client_secret=YOUR_APP_SECRET&fb_exchange_token=YOUR_SHORT_TOKEN</pre>
        <p>Then get Page token again with: <code>GET /me/accounts</code></p>

        <h5><strong>Troubleshooting:</strong></h5>
        <ul>
            <li><strong>Error #3:</strong> App doesn't have required permissions - Add permissions in App Review</li>
            <li><strong>Error #4:</strong> Rate limit - Wait a few minutes</li>
            <li><strong>Error #190:</strong> Token expired - Generate new token</li>
            <li><strong>Error #200:</strong> No permission - Check you're Page Admin</li>
        </ul>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#platform').on('change', function() {
        var platform = $(this).val();
        var helpTexts = {
            'facebook': '<strong>Facebook:</strong> Page ID (e.g., 123456789)',
            'instagram': '<strong>Instagram:</strong> Business Account ID',
            'x': '<strong>X:</strong> Username (e.g., @myaccount)',
            'linkedin': '<strong>LinkedIn:</strong> Person URN (e.g., urn:li:person:xxxxx)',
            'tumblr': '<strong>Tumblr:</strong> Blog hostname (e.g., myblog.tumblr.com)',
            'pinterest': '<strong>Pinterest:</strong> Board ID'
        };
        
        if (helpTexts[platform]) {
            $('#account_id_help').html(helpTexts[platform]);
        }
    });
});
</script>

<?php init_tail(); ?>
</body>
</html>