<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <i class="fa fa-share-alt"></i> Create Social Media Post
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo admin_url('sm_posters'); ?>" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <?php if (empty($connections)) { ?>
                            <div class="alert alert-warning">
                                <p><strong>No active connections found!</strong></p>
                                <a href="<?php echo admin_url('sm_posters/connections'); ?>" class="btn btn-primary">
                                    Add Connection
                                </a>
                            </div>
                        <?php } else { ?>

                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

                        <?php echo form_open(current_url()); ?>

                        <div class="row">
                            <!-- LEFT COLUMN: Platforms & Media -->
                            <div class="col-md-5">
                                
                                <!-- Platform Selection -->
                                <div class="form-group">
                                    <label class="control-label">
                                        <span class="text-danger">*</span> Select Platforms to Post
                                    </label>
                                    <div class="platform-selection">
                                        <?php 
                                        $platforms = [
                                            'facebook' => ['name' => 'Facebook', 'icon' => 'fa-facebook', 'color' => '#4267B2'],
                                            'instagram' => ['name' => 'Instagram', 'icon' => 'fa-instagram', 'color' => '#E4405F'],
                                            'x' => ['name' => 'X (Twitter)', 'icon' => 'fa-twitter', 'color' => '#000000'],
                                            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fa-linkedin', 'color' => '#0077B5'],
                                            'tumblr' => ['name' => 'Tumblr', 'icon' => 'fa-tumblr', 'color' => '#35465C'],
                                            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fa-pinterest', 'color' => '#BD081C']
                                        ];
                                        
                                        foreach ($platforms as $key => $platform) {
                                            $available = array_filter($connections, function($conn) use ($key) {
                                                return $conn->platform == $key;
                                            });
                                            
                                            $disabled = empty($available) ? 'disabled' : '';
                                            $count = count($available);
                                        ?>
                                        <div class="platform-item <?php echo $disabled; ?>" 
                                             style="border-left: 4px solid <?php echo $platform['color']; ?>;">
                                            <label class="checkbox-inline">
                                                <input type="checkbox" 
                                                       name="platforms[]" 
                                                       value="<?php echo $key; ?>" 
                                                       class="platform-checkbox"
                                                       data-platform="<?php echo $key; ?>"
                                                       <?php echo $disabled; ?>>
                                                <i class="fa <?php echo $platform['icon']; ?>" 
                                                   style="color: <?php echo $platform['color']; ?>; font-size: 20px;"></i>
                                                <strong><?php echo $platform['name']; ?></strong>
                                                <?php if ($count > 0) { ?>
                                                    <span class="badge"><?php echo $count; ?> account<?php echo $count > 1 ? 's' : ''; ?></span>
                                                <?php } else { ?>
                                                    <span class="text-danger"><small>Not connected</small></span>
                                                <?php } ?>
                                            </label>
                                            
                                            <!-- Account selection dropdown (shown when platform is checked) -->
                                            <?php if ($count > 0) { ?>
                                            <div class="account-selection" id="accounts_<?php echo $key; ?>" style="display:none;">
                                                <select name="connections[<?php echo $key; ?>]" class="form-control input-sm">
                                                    <option value="">-- Select Account --</option>
                                                    <?php foreach ($available as $conn) { ?>
                                                        <option value="<?php echo $conn->id; ?>">
                                                            <?php echo $conn->account_name ? $conn->account_name : $conn->account_id; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <!-- Scheduling Options -->
                                <div class="form-group">
                                    <label class="control-label">
                                        <i class="fa fa-clock-o"></i> Post Schedule
                                    </label>
                                    <div class="radio radio-primary">
                                        <input type="radio" name="schedule_type" id="post_now" value="now" checked>
                                        <label for="post_now">
                                            <i class="fa fa-paper-plane"></i> Post Immediately
                                        </label>
                                    </div>
                                    <div class="radio radio-primary">
                                        <input type="radio" name="schedule_type" id="schedule_later" value="schedule">
                                        <label for="schedule_later">
                                            <i class="fa fa-calendar"></i> Schedule for Later
                                        </label>
                                    </div>
                                </div>

                                <!-- Date & Time Picker -->
                                <div class="form-group" id="schedule_fields" style="display:none;">
                                    <label for="scheduled_date" class="control-label">
                                        Select Date & Time
                                    </label>
                                    <div class="row">
                                        <div class="col-md-7">
                                            <input type="text" 
                                                   name="scheduled_date" 
                                                   id="scheduled_date" 
                                                   class="form-control datepicker" 
                                                   placeholder="Select date"
                                                   autocomplete="off">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="time" 
                                                   name="scheduled_time" 
                                                   id="scheduled_time" 
                                                   class="form-control" 
                                                   placeholder="HH:MM">
                                        </div>
                                    </div>
                                    <p class="text-muted mtop5">
                                        <small><i class="fa fa-info-circle"></i> Posts will be published at the scheduled time</small>
                                    </p>
                                </div>

                                <hr>

                                <!-- Media Upload Area -->
                                <div class="form-group">
                                    <label class="control-label">Upload Image/Video (Optional)</label>
                                    <div class="media-upload-area">
                                        <input type="file" 
                                               id="media_file" 
                                               accept="image/jpeg,image/jpg,image/png,image/gif,video/mp4,video/mov,video/avi"
                                               style="display:none;">
                                        
                                        <input type="hidden" name="media_base64" id="media_base64">
                                        <input type="hidden" name="media_type" id="media_type">
                                        <input type="hidden" name="media_mime" id="media_mime">
                                        <input type="hidden" name="media_filename" id="media_filename">
                                        
                                        <div id="upload_placeholder" class="upload-box text-center">
                                            <i class="fa fa-camera" style="font-size: 48px; color: #aaa;"></i>
                                            <p class="text-muted mtop10">Click to upload</p>
                                            <p class="text-muted"><small>JPG, PNG, GIF, MP4, MOV, AVI (Max: 100MB)</small></p>
                                        </div>

                                        <div id="media_preview" style="display:none;">
                                            <div style="position: relative;">
                                                <img id="preview_image" src="" style="max-width: 100%; border-radius: 8px; display:none;">
                                                <div id="preview_video" style="display:none;"></div>
                                                <button type="button" class="btn btn-danger btn-sm" id="remove_media" 
                                                        style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                                                    <i class="fa fa-times"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Link -->
                                <div class="form-group" id="link_field">
                                    <label for="link" class="control-label">
                                        <i class="fa fa-link"></i> Link (Optional)
                                    </label>
                                    <input type="url" name="link" id="link" class="form-control" 
                                           placeholder="https://example.com">
                                </div>

                            </div>

                            <!-- RIGHT COLUMN: Message -->
                            <div class="col-md-7">
                                
                                <!-- Post Message -->
                                <div class="form-group">
                                    <label for="message" class="control-label">
                                        <span class="text-danger">*</span> Post Message
                                    </label>
                                    <textarea name="message" 
                                              id="message" 
                                              rows="15" 
                                              class="form-control" 
                                              placeholder="What's on your mind? Write your message here..."
                                              required
                                              style="font-size: 14px; line-height: 1.6;"><?php echo set_value('message'); ?></textarea>
                                    <p class="text-muted">
                                        <small><span id="char_count">0</span> characters</small>
                                    </p>
                                </div>

                                <!-- Platform-Specific Notes -->
                                <div class="alert alert-info">
                                    <strong><i class="fa fa-info-circle"></i> Platform Guidelines:</strong>
                                    <ul class="list-unstyled mtop10" style="margin-bottom: 0; font-size: 12px;">
                                        <li><strong>Facebook:</strong> No character limit</li>
                                        <li><strong>Instagram:</strong> 2,200 characters max, hashtags recommended</li>
                                        <li><strong>X (Twitter):</strong> 280 characters max</li>
                                        <li><strong>LinkedIn:</strong> 3,000 characters max</li>
                                        <li><strong>Tumblr:</strong> No character limit</li>
                                        <li><strong>Pinterest:</strong> 500 characters recommended</li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <button type="submit" class="btn btn-lg btn-primary" id="submit_btn">
                                    <i class="fa fa-paper-plane"></i> <span id="btn_text">Post Now</span>
                                </button>
                                <button type="button" class="btn btn-lg btn-default" onclick="window.location.href='<?php echo admin_url('sm_posters/posts'); ?>'">
                                    <i class="fa fa-save"></i> Save as Draft
                                </button>
                                <a href="<?php echo admin_url('sm_posters'); ?>" class="btn btn-lg btn-default">
                                    Cancel
                                </a>
                            </div>
                        </div>

                        <?php echo form_close(); ?>

                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.platform-selection {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    background: #fafafa;
}

.platform-item {
    padding: 12px;
    margin-bottom: 10px;
    background: white;
    border-radius: 6px;
    transition: all 0.3s;
}

.platform-item:not(.disabled):hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.platform-item.disabled {
    opacity: 0.5;
    background: #f5f5f5;
}

.platform-item label {
    margin: 0;
    cursor: pointer;
    width: 100%;
}

.account-selection {
    margin-top: 10px;
    padding-left: 30px;
}

.upload-box {
    border: 2px dashed #ccc;
    padding: 40px 20px;
    cursor: pointer;
    border-radius: 8px;
    background: #f9f9f9;
    transition: all 0.3s;
}

.upload-box:hover {
    border-color: #4267B2;
    background: #f0f2f5;
}

#preview_image {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<?php init_tail(); ?>

<script>
(function() {
    'use strict';
    
    var hasMedia = false;

    // Initialize datepicker
    $('#scheduled_date').datepicker({
        format: 'yyyy-mm-dd',
        startDate: new Date(),
        autoclose: true
    });

    // Schedule type change
    $('input[name="schedule_type"]').on('change', function() {
        if ($(this).val() === 'schedule') {
            $('#schedule_fields').slideDown();
            $('#btn_text').text('Schedule Post');
            $('#scheduled_date, #scheduled_time').prop('required', true);
        } else {
            $('#schedule_fields').slideUp();
            $('#btn_text').text('Post Now');
            $('#scheduled_date, #scheduled_time').prop('required', false);
        }
    });

    // Platform checkbox handling
    $('.platform-checkbox').on('change', function() {
        var platform = $(this).data('platform');
        var accountSelect = $('#accounts_' + platform);
        
        if ($(this).is(':checked')) {
            accountSelect.slideDown();
            accountSelect.find('select').prop('required', true);
        } else {
            accountSelect.slideUp();
            accountSelect.find('select').prop('required', false);
        }
    });

    // Upload functionality
    $(document).on('click', '#upload_placeholder', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#media_file')[0].click();
    });

    $('#media_file').on('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;

        var fileSize = file.size / (1024 * 1024);
        if (fileSize > 100) {
            alert('File is too large! Maximum size is 100MB.');
            $(this).val('');
            return;
        }

        var fileType = file.type;
        var fileName = file.name;

        $('#upload_placeholder').hide();
        $('#media_preview').show();
        $('#link_field').slideUp();

        $('#media_type').val(fileType.startsWith('image/') ? 'image' : 'video');
        $('#media_mime').val(fileType);
        $('#media_filename').val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
            var base64 = e.target.result.split(',')[1];
            $('#media_base64').val(base64);
            hasMedia = true;

            if (fileType.startsWith('image/')) {
                $('#preview_image').attr('src', 'data:' + fileType + ';base64,' + base64).show();
                $('#preview_video').hide();
            } else if (fileType.startsWith('video/')) {
                $('#preview_image').hide();
                $('#preview_video').html(
                    '<div class="alert alert-success text-center" style="margin: 0;">' +
                    '<i class="fa fa-video-camera fa-3x"></i>' +
                    '<p class="mtop10"><strong>' + fileName + '</strong></p>' +
                    '<p>Size: ' + fileSize.toFixed(2) + ' MB</p>' +
                    '</div>'
                ).show();
            }
        };
        reader.readAsDataURL(file);
    });

    $(document).on('click', '#remove_media', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $('#media_file').val('');
        $('#media_base64, #media_type, #media_mime, #media_filename').val('');
        $('#media_preview').hide();
        $('#upload_placeholder').show();
        $('#preview_image').attr('src', '').hide();
        $('#preview_video').html('').hide();
        hasMedia = false;
        $('#link_field').slideDown();
    });

    // Character counter
    $('#message').on('input', function() {
        $('#char_count').text($(this).val().length);
    });

    // Form validation
    $('form').on('submit', function(e) {
        var message = $('#message').val().trim();
        var checkedPlatforms = $('.platform-checkbox:checked').length;
        
        if (message.length === 0) {
            e.preventDefault();
            alert('Please enter a message for your post');
            $('#message').focus();
            return false;
        }

        if (checkedPlatforms === 0) {
            e.preventDefault();
            alert('Please select at least one platform to post');
            return false;
        }

        $('#submit_btn').prop('disabled', true).html(
            '<i class="fa fa-spinner fa-spin"></i> Processing...'
        );
    });
})();
</script>

</body>
</html>