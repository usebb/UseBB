/*
	Copyright (C) 2003-2005 UseBB Team
	http://www.usebb.net
	
	$Header$
	
	This file is part of UseBB.
	
	UseBB is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	UseBB is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with UseBB; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//
//	Apply BBCode and smilies to selection.
//	
//	This function is borrowed from DokuWiki. Uses portions of code by:
//	- phpBB development team
//	- MediaWiki development team
//	- Andreas Gohr <andi@splitbrain.org>
//	- Jim Raynor <jim_raynor@web.de>
//
function insert_tags(tagOpen, tagClose, sampleText) {
	var txtarea = document.postform.content;
	// IE
	if(document.selection && !is_gecko) {
		var theSelection = document.selection.createRange().text;
		var replaced = true;
		if(!theSelection){
			replaced = false;
			theSelection=sampleText;
		}
		txtarea.focus();
 
		// This has change
		text = theSelection;
		if(theSelection.charAt(theSelection.length - 1) == " "){// exclude ending space char, if any
			theSelection = theSelection.substring(0, theSelection.length - 1);
			r = document.selection.createRange();
			r.text = tagOpen + theSelection + tagClose + " ";
		} else {
			r = document.selection.createRange();
			r.text = tagOpen + theSelection + tagClose;
		}
		if(!replaced){
			r.moveStart('character',-text.length-tagClose.length);
			r.moveEnd('character',-tagClose.length);
		}
		r.select();
	// Mozilla
	} else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
		var replaced = false;
		var startPos = txtarea.selectionStart;
		var endPos   = txtarea.selectionEnd;
		if(endPos - startPos) replaced = true;
		var scrollTop=txtarea.scrollTop;
		var myText = (txtarea.value).substring(startPos, endPos);
		if(!myText) { myText=sampleText;}
		if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
			subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
		} else {
			subst = tagOpen + myText + tagClose;
		}
		txtarea.value = txtarea.value.substring(0, startPos) + subst +
			              txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.focus();
 
		//set new selection
		if(replaced){
			var cPos=startPos+(tagOpen.length+myText.length+tagClose.length);
			txtarea.selectionStart=cPos;
			txtarea.selectionEnd=cPos;
		}else{
			txtarea.selectionStart=startPos+tagOpen.length;   
			txtarea.selectionEnd=startPos+tagOpen.length+myText.length;
			txtarea.scrollTop=scrollTop;
		}
	// All others
	} else {
		var copy_alertText=alertText;
		var re1=new RegExp("\\$1","g");
		var re2=new RegExp("\\$2","g");
		copy_alertText=copy_alertText.replace(re1,sampleText);
		copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);
		var text;
		if (sampleText) {
			text=prompt(copy_alertText);
		} else {
			text="";
		}
		if(!text) { text=sampleText;}
		text=tagOpen+text+tagClose;
		//append to the end
		txtarea.value += "\n"+text;

		// in Safari this causes scrolling
		if(!is_safari) {
			txtarea.focus();
		}

	}
	// reposition cursor if possible
	if (txtarea.createTextRange) txtarea.caretPos = document.selection.createRange().duplicate();
}

//
// Shortcut for smiley adding code
//
function insert_smiley(code) {
	insert_tags('', code, '');
}
