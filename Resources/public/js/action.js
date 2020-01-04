window.onload = function(){
    
    //Set focus always on the input
    var input= document.getElementsByTagName("input")[0];
    input.focus();
    input.onblur= function() {
        setTimeout(function() {
            input.focus();
        }, 0);
    };
    
    //On form submit, hide messages (if any) and show spinner
    document.getElementsByTagName("form")[0].onsubmit = function(){
        var spinner_container = document.getElementById("spinner_container");
        var messages = document.getElementById("messages");
        
        if(messages){messages.className = 'hidden';}        
        spinner_container.className = '';
    };
    
};