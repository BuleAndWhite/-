(function ($) {
    // dialogbox Method
    $.fn.dialogbox = function ($options, $callback) {
        // Create a unique ID
        var $id = '#dialogbox_1';
        for(var $i = 2; $($id).length; $i++) $id = '#dialogbox_' + $i;
        // Add dialogbox to element
        var $DOM_ID = $id.substr(1);
        this.append('<!-- Afraware Dialogbox -->' + '<div id="' + $DOM_ID + '">' +
        '<div class="dialogbox-back">' + '<div class="dialogbox-main">' +
        '<div class="dialogbox-close">x</div>' + '<div class="dialogbox-title"></div>' +
        '<div class="dialogbox-message"></div>' + '<div class="dialogbox-prompt"></div>' +
        '<div class="dialogbox-button-bar"></div>' + '</div>' + '</div>' + '</div>');
        // Options
        var $settings = $.extend({
            type: 'normal',
            title: 'Message',
            message: 'WOW! It\'s a message!',
            direction: 'ltr',
            placeholder: null,
            options: '',
            splitter: ','
        }, $options);
        // Set Direction
        switch($settings['direction']) {
            case 'rtl':
                $($id + ' .dialogbox-main').css({
                    'border-right-width': '5px'
                });
                $($id + ' .dialogbox-close').css({
                    'left': '15px',
                    'right': 'none'
                });
                $($id + ' .dialogbox-main').css({
                    'direction': 'rtl'
                });
                break;
            default:
                $($id + ' .dialogbox-main').css({
                    'border-left-width': '5px'
                });
                $($id + ' .dialogbox-close').css({
                    'right': '0px',
                    'left': 'none'
                });
                $($id + ' .dialogbox-main').css({
                    'direction': 'ltr'
                });
                break;
        }
        // Draw Basic
        switch($settings['type']) {
            // Messages
            case 'normal':
            case 'success':
            case 'warning':
            case 'error':
                $($id + ' .dialogbox-prompt').hide();
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn">OK</div>');
                break;
            // Questions
            case 'pay_way':
                $($id + ' .dialogbox-prompt').hide();
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn dialogbox-btn1	">支付宝</div>　　').append(
                    '<div class="dialogbox-btn dialogbox-btn1">微信</div>');
                break;
            case 'yes/no':
                $($id + ' .dialogbox-prompt').hide();
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn">yes</div>　　').append(
                    '<div class="dialogbox-btn">no</div>');
                break;
            case 'ok/cancel':
                $($id + ' .dialogbox-prompt').hide();
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn">确定</div>').append(
                    '<div class="dialogbox-btn">取消</div>');
                break;
            case 'retry/ignore/abort':
                $($id + ' .dialogbox-prompt').hide();
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn">Retry</div>').append(
                    '<div class="dialogbox-btn">Ignore</div>').append('<div class="dialogbox-btn">Abort</div>');
                break;
            // Prompts
            case 'text':
                var $placeholder = ($settings['placeholder'] == null) ? 'Type...' : $settings['placeholder'];
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn">OK</div>');
                $($id + ' .dialogbox-prompt').html('').show().append('<input type="text" placeholder="' +
                $placeholder + '">');
                break;
            case 'select':
                var $placeholder = ($settings['placeholder'] == null || $settings['placeholder'] == '') ?
                    'Select one...' : $settings['placeholder'];
                var $array = $settings['options'].split($settings['splitter']);
                $($id + ' .dialogbox-button-bar').html('').append('<div class="dialogbox-btn">OK</div>');
                $($id + ' .dialogbox-prompt').html('').show().append(
                    '<select title="Options"><option value="_placeholder">' + $placeholder +
                    '</option></select>');
                $.each($array, function ($k, $value) {
                    $value = $.trim($value);
                    if($value != '') $($id + ' .dialogbox-prompt select').append('<option>' + $value +
                    '</option>');
                });
                break;
        }
        // Draw Details
        var $border_color = 'rgb(90,90,90)';
        switch($settings['type']) {
            case 'success':
                $border_color = 'rgb(39,174,96)';
                break;
            case 'warning':
            case 'retry/ignore/abort':
                $border_color = 'rgb(243,156,18)';
                break;
            case 'error':
                $border_color = 'rgb(192,57,43)';
                break;
        }
        $($id + ' .dialogbox-main').css({
            'border-color': $border_color
        });
        // Set Title and Message
        $($id + ' .dialogbox-title').html($settings['title']);
        $($id + ' .dialogbox-message').html($settings['message']);
        // Display
        $($id + ' .dialogbox-back').fadeIn('fast');
        $($id + ' .dialogbox-main').fadeIn('fast');
        // Callbacks
        if($callback && $callback.constructor && $callback.call && $callback.apply) {
            // Close
            $(document).on("click", $id + ' .dialogbox-close', function () {
                $($id).fadeOut("slow");
                $callback("close");
                $($id).remove();
            });
            // Buttons
            if($settings['type'] == 'text') {
                $(document).on('click', $id + ' .dialogbox-btn', function () {
                    var $btn = 'ok';
                    var $return = $($id + ' input').val();
                    if($return == '') {
                        $btn = "close";
                        $callback($btn);
                        $($id).remove();
                    } else {
                        $callback($btn, $return);
                        $($id).remove();
                    }
                });
            } else if($settings['type'] == 'select') {
                $(document).on('click', $id + ' .dialogbox-btn', function () {
                    var $btn = "ok";
                    var $return = $($id + ' select').val();
                    if($return == "_placeholder") {
                        $btn = "close";
                        $callback($btn);
                        $($id).remove();
                    } else {
                        $callback($btn, $return);
                        $($id).remove();
                    }
                });
            } else {
                $(document).on('click', $id + ' .dialogbox-btn', function () {
                    $callback($(this).html().toLowerCase());
                    $($id).remove();
                });
            }
        } else {
            $(document).on("click", $id + ' .dialogbox-close', function () {
                $($id).remove();
            }).on("click", $id + ' .dialogbox-btn', function () {
                $($id).remove();
            });
        }
    };
}(jQuery));
// The End!