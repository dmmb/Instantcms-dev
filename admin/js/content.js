function showIns(){

	document.getElementById('material').style.display = 'none';
	document.getElementById('album').style.display = 'none';
	document.getElementById('photo').style.display = 'none';
	document.getElementById('price').style.display = 'none';
	document.getElementById('blank').style.display = 'none';
	document.getElementById('frm').style.display = 'none';	
	document.getElementById('filelink').style.display = 'none';	
	document.getElementById('include').style.display = 'none';		

	needDiv = document.addform.ins.options[document.addform.ins.selectedIndex].value;
	
	document.getElementById(needDiv).style.display = "block";

}

function checkGroupList(){

	if(document.addform.is_public.checked){
		document.getElementById('grp').style.display = 'none';
	} else {
		document.getElementById('grp').style.display = 'block';
	}

}
function insertTag(kind){
	var oEditor = FCKeditorAPI.GetInstance('content') ;
	
	var text = ''; 
	
	if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG ) {

		if (kind=='material'){			
			text = '{��������=' + document.addform.m.options[document.addform.m.selectedIndex].text + '}';
		}
		if (kind=='photo'){			
			text = '{����=' + document.addform.f.options[document.addform.f.selectedIndex].text + '}';
		}
		if (kind=='album'){			
			text = '{������=' + document.addform.a.options[document.addform.a.selectedIndex].text + '}';
		}
		if (kind=='price'){			
			text = '{�����=' + document.addform.p.options[document.addform.p.selectedIndex].text + '}';
		}
		if (kind=='frm'){			
			text = '{�����=' + document.addform.fm.options[document.addform.fm.selectedIndex].text + '}';
		}
		if (kind=='blank'){			
			text = '{�����=' + document.addform.b.options[document.addform.b.selectedIndex].text + '}';
		}
		if (kind=='include'){			
			text = '{����=' + document.addform.i.value + '}';
		}	
		if (kind=='filelink'){			
			text = '{�������=' + document.addform.fl.value + '}';
		}	
		if (kind=='banpos'){			
			text = '{������=' + document.addform.ban.value + '}';
		}	
		if (kind=='page'){			
			text = '{pagebreak}';
		}

		oEditor.InsertHtml( text ) ;
	}
	else alert( '����������� �������� � ���������� �����!' ) ;
}

function InsertPagebreak()
{
	// Get the editor instance that we want to interact with.
	var oEditor = FCKeditorAPI.GetInstance('content') ;

	// Check the active editing mode.
	if ( oEditor.EditMode == FCK_EDITMODE_WYSIWYG )
	{
		// Insert the desired HTML.
		oEditor.InsertHtml( '{pagebreak}' ) ;
	}
	else
		alert( '����������� �������� � ���������� �����!' ) ;
}