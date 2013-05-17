function toggle() {
    var nav = document.getElementById('site-navigation');
    if (!nav)
	return;

    var top_level = nav.getElementsByTagName('li');
    if (top_level.length == 0)
	return;
    var i;

    for (i = 0; i < top_level.length; ++i) {
	top_level[i].onmouseover = function(){
	    this.className += ' toggled_on';
	};
	top_level[i].onmouseout = function() {
	    this.className = this.className.replace(/\btoggled_on\b/,'');
	};
    }
}

toggle();


function hilight() {
    var nav = document.getElementById('site-navigation');
    if (!nav)
	return;

    var top_level = nav.getElementsByTagName('li');
    if (top_level.length == 0)
	return;

    var i;
    for (i = 0; i < top_level.length; ++i) {
	
    }
}
