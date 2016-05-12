<?php


?>

<html>
	<head>
		<title>Open Legal</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script>

			
			$( document ).ready(function() {
    			
    			//load txt contract

    			$.ajax({
				  url: 'test.txt',
				  dataType: "text",
				  success: function(data){
				     //load content to div
				     $("#contract_text").html(data);
				     $("#contract_text_container").append('<div id="contract_action" class="edit">Edit</div>');
				  }
				});

			});
			
			//created editable contract
			$(document).on("click", ".edit", function(){
				//remove edit button
				$("#contract_action").remove();

				// save the html and height 
				var divHtml = $("#contract_text").html();
				var divHeight = $("#contract_text").height();
				var divWidth = $("#contract_text").width();
				// create a dynamic textarea
				var editableText = $("<textarea id='contract_textarea'></textarea>");
				
				// fill the textarea with the div's text
				editableText.val(divHtml);
				
				// replace the div with the textarea and set height

				$("#contract_text").replaceWith(editableText);
				$("#contract_textarea").css({"height":divHeight,"width":divWidth});
				$("#contract_text_container").append('<div id="contract_action" class="save">Save</div>');

			});

			//save edited contract
			$(document).on("click", ".save", function(){
				
				var contract = $("#contract_textarea").val();

				$.ajax({
				    url: 'save_txt.php',
				    type: 'POST',
				    data: { data: contract },
				    success: function(result) {
				        alert(result);
				    }
				});

				$("#contract_action").remove();
				$("#contract_text_container").append('<div id="contract_action" class="edit">Edit</div>');

			});


		</script>
		<style>
			body {
				margin:0;
				font-size: 16px;
				font-family:verdana;
			}
			#top {
				background: black;
				color:white;
				font-size:36px;
				padding:6px 9px;
				letter-spacing: -3px;
			}
			#contract_text_container {
				position: relative;
				border-style: solid;
    			border-width: 1px 2px 2px 1px;
    			border-color:#181818;
				padding:36px;
				white-space: pre-line;
				width:80%;
				margin:auto;
				background:white;
				z-index: 1;
				font-family:Times !important;
			}

			#contract_textarea {
				display:block;
				font-family: times;
				font-size:16px;
				border: none;

			}

			#contract_action {
				width:72px;
				height:36px;
				position: absolute;
				top:11px;
				right:20px;
				z-index: 3;
				font-size:22px;
				color:white;
				text-align: center;
				line-height: 36px;
				cursor: pointer;
				font-family:verdana !important;
			}

			.edit {
				background-color: #1ed760;	
			}

			.save {
				background-color: #ce5054;
			}

			#contract_container {
				padding:30px 0;
				background:#f2f2f2;
			}
		</style>
	</head>
	<body>
		<div id="top">
			<b>openlegal</b>
		</div>
		<div id="contract_container">
			<div id="contract_text_container"><div id="contract_text"></div></div>
		</div>
	</body>
</html>