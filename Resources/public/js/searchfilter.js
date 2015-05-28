jQuery(function($){

    $('#search-media').keyup(function(event)
    {
        var $input = $(this);
        var $bodyList = $('#medias-list');
        var $val = $input.val();
        //afficher toutes les lignes si acunes valeurs
        if($val == ''){
            $bodyList.find('tr').show();
            return true;
        }
        var $regexp = '\\b(.*)';

        for(var i in $val)
        {
            $regexp += '(' + $val[i] + ')(.*)';
        }

        $regexp += '\\b';
        $bodyList.find('td>span').each(function(){
            var span = $(this);
            var results = span.text().match(new RegExp($regexp,'i'));
            if(!results){
                span.parents('tr').hide();
            }
        });
    })



});