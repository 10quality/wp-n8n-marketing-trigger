(function($){
    'use strict';
    function setFeedback(text, isError) {
        var $feedback = $('#n8n-mt-trigger-feedback');
        if (!$feedback.length) {
            return;
        }
        $feedback.text(text || '');
        $feedback.css('color', isError ? '#b32d2e' : '#2271b1');
    }
    function setLoading($buttons, loading) {
        $buttons.prop('disabled', loading);
        if (loading) {
            $buttons.addClass('disabled');
        } else {
            $buttons.removeClass('disabled');
        }
    }
    $(function(){
        if (typeof n8nMtCampaignTrigger === 'undefined') {
            return;
        }
        var cfg = n8nMtCampaignTrigger;
        var $buttons = $('.n8n-mt-trigger-button');
        $buttons.on('click', function(){
            var mode = $(this).data('mode');
            var action = cfg.actions && cfg.actions[mode] ? cfg.actions[mode] : '';
            if (!action) {
                setFeedback(cfg.messages.genericError, true);
                return;
            }
            setFeedback(cfg.messages.sending, false);
            setLoading($buttons, true);
            $.post(cfg.ajaxUrl, {
                action: action,
                n8n_mt_nonce: cfg.nonce,
                campaign_id: cfg.campaignId
            }).done(function(response){
                if (response && response.success && response.data && response.data.message) {
                    setFeedback(response.data.message, false);
                    return;
                }
                var failedMessage = response && response.data && response.data.message
                    ? response.data.message
                    : cfg.messages.genericError;
                setFeedback(failedMessage, true);
            }).fail(function(){
                setFeedback(cfg.messages.genericError, true);
            }).always(function(){
                setLoading($buttons, false);
            });
        });
    });
})(jQuery);
