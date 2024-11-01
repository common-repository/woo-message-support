function submitFeedback() {

	if(jQuery('#txtWMSSubject') != undefined && jQuery('#txtWMSSubject').val() == '') {
		alert('Please enter subject');
		return false;
	}

	if(jQuery('#taWMSFeedback').val() == '') {
		alert('Please enter message');
		return false;
	}

}