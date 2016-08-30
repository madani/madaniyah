'Pandamp.Util'.namespace();

Pandamp.Util = function() {};

/**
 * Generate shortTitle for given string
 * 
 * @param string str
 * @return string
 */
Pandamp.Util.generateSlug = function(str) {
	str = str.replace(/^\s+|\s+$/g, '');
  	var from = "ÁÀẠẢÃĂẮẰẶẲẴÂẤẦẬẨẪáàạảãăắằặẳẵâấầậẩẫóòọỏõÓÒỌỎÕôốồộổỗÔỐỒỘỔỖơớờợởỡƠỚỜỢỞỠéèẹẻẽÉÈẸẺẼêếềệểễÊẾỀỆỂỄúùụủũÚÙỤỦŨưứừựửữƯỨỪỰỬỮíìịỉĩÍÌỊỈĨýỳỵỷỹÝỲỴỶỸĐđÑñÇç·/_,:;";
  	var to   = "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaooooooooooooooooooooooooooooooooooeeeeeeeeeeeeeeeeeeeeeeuuuuuuuuuuuuuuuuuuuuuuiiiiiiiiiiyyyyyyyyyyddnncc------";
  	
  	for (var i = 0, l = from.length ; i < l; i++) {
    	str = str.replace(new RegExp(from[i], "g"), to[i]);
  	}
  	str = str.replace(/[^a-zA-Z0-9 -]/g, '').replace(/\s+/g, '-').toLowerCase();
  	return str;
};
