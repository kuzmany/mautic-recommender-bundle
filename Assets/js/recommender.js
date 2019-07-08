Mautic.recommenderOnLoad = function (container, response) {
    var availableFilters = mQuery('div.dwc-filter').find('select[data-mautic="available_filters"]');
    Mautic.activateChosenSelect(availableFilters, false);

    Mautic.leadlistOnLoad('div.dwc-filter');
}
mQuery('.recommender-preview .editor-basic').on('froalaEditor.contentChanged', function(){
        Mautic.recommenderUpdatePreview();
});

mQuery(document).on('blur', '.recommender-preview input:text', function(){
    Mautic.recommenderUpdatePreview();
});

mQuery(document).on('change', '.recommender-preview select', function(){
    Mautic.recommenderUpdatePreview();
});

mQuery(document).on('change', '.recommender-preview input:radio', function(){
    Mautic.recommenderUpdatePreview();
});

Mautic.recommenderUpdatePreview = function () {
    mQuery('#recommender-preview').fadeTo('normal', 0.4);
    mQuery('#recommender-preview-loader').show();
    var data = mQuery('form[name=recommender_templates]').formToArray();
    Mautic.ajaxActionRequest('plugin:recommender:generatePreview', data, function (response) {
        if(response.content) {
            mQuery('#recommender-preview').html(response.content);
        }
        mQuery('#recommender-preview').fadeTo('normal', 1);
        mQuery('#recommender-preview-loader').hide();
    });
}


Mautic.reloadExample = function (el) {
    Mautic.loadContent(mQuery(el).parents('form').attr('action')+'?tmpl=template', '', 'POST', '.contact-options', false, false, mQuery(el).parents('form').formToArray());
}


/**
 * Enables/Disables email preview and edit. Can be triggered from campaign or form actions
 * @param opener
 * @param origin
 */
Mautic.disabledTemplateAction = function(opener, origin) {
    if (typeof opener == 'undefined') {
        opener = window;
    }
    var recommender = opener.mQuery(origin);
    if (recommender.length == 0) return;
    var recommenderId = recommender.val();
    var disabled = recommenderId === '' || recommenderId === null;

    opener.mQuery('[id$=_editRecommenderButton]').prop('disabled', disabled);
};



Mautic.standardRecommenderUrl = function(options) {
    if (options && options.windowUrl && options.origin) {
        var url = options.windowUrl;
        var editEmailKey = '/recommenderTemplate/edit/recommenderId';
        if (url.indexOf(editEmailKey) > -1) {
            options.windowUrl = url.replace('recommenderId', mQuery(options.origin).val());
        }
    }

    return options;
};




