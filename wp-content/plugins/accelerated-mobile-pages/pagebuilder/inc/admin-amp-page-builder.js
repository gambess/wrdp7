jQuery( document ).ready( function( $ ){
    $( "#sorted_rows" ).sortable({
    	placeholder: "ui-state-highlight",
    	handle  : '.amppb-handle',
		cursor  : 'grabbing',
		axis    : 'y',
		update  : function(){
			var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
			$(this).find('div.amppb-row').each(function(indexKey,val){
				var indexOfRow = $(this).attr('id').replace('conatiner-','');
				$.each(ploatedStructure.rows,function(k,columnVal){
					if(columnVal.id==indexOfRow){
						columnVal.index = indexKey+1;
					}
				});
			});
			storeJsonDataInput(ploatedStructure)
		}
    });
    $( ".draggable" ).draggable({
    	helper: "clone",
    	revert:'invalid',
    	start: function(event, ui){
    		
		},
		stop: function(event, ui){

		}
    });

    $( ".droppable" ).droppable({
    	accept:'.amppb-actions > span',
    	classes: {
	        "ui-droppable-active": "ui-state-active",
	        "ui-droppable-hover": "ui-state-hover"
	      },
		drop: function( event, ui ) {
			
			$containerid = parseInt($('#amppb-actions-container').attr('data-containerid'));
			$('#amppb-actions-container').attr('data-containerid',$containerid+1);


			var templateclass = ui.draggable.attr("data-template");
			var template = '.amppb-templates > .amppb-' + templateclass;
			$( template ).clone().attr('id','conatiner-'+$containerid).appendTo(this);
			
			var noOfCell = 1;
			if(templateclass=='col-2'){ noOfCell=2; }
			var rowContainer = {
				'id':$containerid,
				'index':$containerid,
				'cells': noOfCell,
				'cell_data': [],
				'data':{}
			};
			var ploatedStructure = ($('#amp-page-builder-data').val());
			if(ploatedStructure!=''){
				sampleData = JSON.parse(ploatedStructure);
			}else{
				sampleData = sampleData
			}
			sampleData.rows.push(rowContainer);
			sampleData['totalrows'] = $containerid+1;

			//store in input box
    		storeJsonDataInput(sampleData);


			if($(this).find('.dummy')){
				$(this).find('.dummy').remove();
			}

			moduleLoad();
		}
    });
    $( document.body ).on( 'click', '.amppb-remove', function(e){
			 e.preventDefault();
			if(confirm("Are you sure want to delete Row?")){
				/* Delete Row */
				var containerId = $( this ).parents( '.amppb-row' ).attr('id').replace("conatiner-","");
				var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
				
				$.each(ploatedStructure.rows,function(key,rowData){
					if(rowData.id==containerId){
						ploatedStructure.rows.splice(key, 1);
						return false;
					}
				});
				//console.log(ploatedStructure);
				storeJsonDataInput(ploatedStructure);
				
				$( this ).parents( '.amppb-row' ).remove();
			}
		});
	
	$( document.body ).on('click', '.remove-module', function(e){
		 e.preventDefault();
		if(confirm("Are you sure want to delete Module?")){
			/* Delete Row */
			var containerdetails = $(this).parents('.amppb-tc-footer').find("#ampb-parents-dialog").attr('data-container');
		
			containerdetails = containerdetails.split('--');
			
			var containerId = containerdetails[0];
			var moduleId = containerdetails[1];
			
			//To remove From main structure
			var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
			$.each(ploatedStructure.rows,function(key,rowData){
				if(rowData.id==containerId){
					$.each(rowData.cell_data,function(cellkey,cellData){
						if(cellData.cell_id==moduleId){
							ploatedStructure.rows[key].cell_data.splice(cellkey, 1);
							return false;
						}
					});
					//ploatedStructure.rows.splice(key, 1);
					return false;
				}
			});
			storeJsonDataInput(ploatedStructure);
			$("#sorted_rows").find("#conatiner-"+containerId).find("#module-"+moduleId).remove();
			tb_remove();
			//$(this).parents(".amppb-module").remove();
			callToRemoveHasModule();
		}
		
	});

    $(document.body).on('click', ".rowBoxContainer", function(e){
		 e.preventDefault();
		/* ON open row Setting thickbox */
		var containerId = $( this ).parents( '.amppb-row' ).attr('id').replace("conatiner-","");
		$('#amppb-rowsetting').attr('data-current-container',containerId);
		
		$(".amp-pb-rowsetting-content").html(''); 
		var popupContents = $(this).attr('data-template');
		$("#amppb-rowsetting").attr('data-template',popupContents);
		popupContents = JSON.parse(popupContents);
		var popupHtml = '';
		var upload = false; var editor = [];
		var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
		$.each(ploatedStructure.rows,function(rowkey,rowData){
			if(rowData.id==containerId){
				//ploatedStructure.rows[rowkey].data = {};
				$.each(popupContents.fields, function(fieldsName,fieldReplace){
					
					if(typeof ploatedStructure.rows[rowkey].data[fieldReplace.name]!='undefined'){
						fieldReplace.default = ploatedStructure.rows[rowkey].data[fieldReplace.name];
					}else{
						ploatedStructure.rows[rowkey].data[fieldReplace.name] = fieldReplace.default;
					}
					
					var htmlFields = $('.amppb-fields-templates').find("#"+fieldReplace.type).html();
					var id = fieldReplace.name;
					popupHtml += htmlFields.replace(/{name}/g,fieldReplace.name).replace(/{label}/g,fieldReplace.label).replace(/{id}/g,id).replace(/{default_value}/g, decodeURI(fieldReplace.default));
					if(popupHtml.indexOf('{options}')!==-1){
						popupHtml += popupHtml.replace(/{options}/g, decodeURI(fieldReplace.options));
					}
					//To load action of fields
					switch(fieldReplace.type){
						case 'upload':
							upload = true;
							break;
						case 'text-editor':
							editor.push(id);
							break; 
					}
					
					
								
				});
			return false;
			}
		});
		storeJsonDataInput(ploatedStructure);
	
		$(".amp-pb-rowsetting-content").html(popupHtml); 
		editorJs(editor);
	});
	//Save data of row settings
	$( document.body ).on('click', "#amppb-rowsetting", function(e){
		e.preventDefault();
		console.log("Save button #amppb-rowsetting has clicked");
		var containerId = $(this).attr('data-current-container');
		
		var popupContents = $(this).attr('data-template');
		
		var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
		
		popupContents = JSON.parse(popupContents);
		$.each(ploatedStructure.rows,function(rowkey,rowData){
			ploatedStructure.rows[rowkey].data = {};
			if(rowData.id==containerId){
				$.each(popupContents.fields, function(fieldsName,fieldReplace){
					var userValue = $("#"+fieldReplace.name).val();
					fieldReplace.default = userValue;
					
						ploatedStructure.rows[rowkey].data[fieldReplace.name] = userValue;
							
				})
				return false;
			}
		});
			storeJsonDataInput(ploatedStructure);
		//Restore New values in start setting button
		$("#conatiner-"+containerId).find('.rowBoxContainer').attr('data-template',JSON.stringify(popupContents));
		
		tb_remove();
	});
	
	//save the content of popup
	$( document.body ).on('click', ".amppb-rowData-content", function(){
		var containerdetails = $(this).parents('.buttons-groups').find("#ampb-parents-dialog").attr('data-container');
		
		containerdetails = containerdetails.split('--');

		//Grab value of current modules
		var moduledetails = $(this).parents('.buttons-groups').find("#ampb-parents-dialog").attr('data-modules');
		

		moduledetails = JSON.parse(moduledetails);

		var moduleJson = JSON.parse($('#module-'+containerdetails[1]).find('#selectedModule').val());
		
		$.each(moduleJson.fields, function(fieldtype,modData){
			var fieldIdentifier = modData.name+'-'+containerdetails[0]+'-'+containerdetails[1];
			if(modData.type=='text-editor'){
				modData.default = encodeURI(tinymce.get(fieldIdentifier).getContent().replace("'","\'"));
			}if(modData.type=='select'){
				modData.default = encodeURI($('#'+fieldIdentifier).val());
			}else{
				modData.default = encodeURI($('#'+fieldIdentifier).val().replace("'","\'"));
			}
			
		});
		console.log(JSON.stringify(moduleJson));
		$('#module-'+containerdetails[1]).find('#selectedModule').val(JSON.stringify(moduleJson))

		/*var fieldValue = [];
		$.each(moduledetails.fields, function(fieldtype,modData){
			var fieldIdentifier = modData.name+'-'+containerdetails[0]+'-'+containerdetails[1];
			fieldValue[modData.name] = $('#'+fieldIdentifier).val();
		});*/

		//Store it new values in json for DB
		var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
		$.each(ploatedStructure.rows,function(k,row){
			if(row.id==containerdetails[0]){
				if(row.cell_data.length>0){
					$.each(row.cell_data,function(k,cells){
						if(cells.cell_id==containerdetails[1]){

							$.each(moduledetails.fields, function(fieldtype,modData){
								var fieldIdentifier = modData.name+'-'+containerdetails[0]+'-'+containerdetails[1];
								if(modData.type=='text-editor'){
									cells[modData.name] = encodeURI(tinymce.get(fieldIdentifier).getContent().replace("'","\'"));
								}else{
									cells[modData.name] = encodeURI($('#'+fieldIdentifier).val().replace("'","\'"));
								}
								//cells[modData.name] = $('#'+fieldIdentifier).val();
							});

						}
					})
				}
			}
		});
		storeJsonDataInput(ploatedStructure);
		tb_remove();
	});
	
	
	
	//Thickbox on open
	$(document.body).on('click', ".boxContainer", function(e){
		e.preventDefault();
		$(".amp-pb-module-content").html("");
		var conatinerId = $(this).parents(".amppb-row").attr("id").replace('conatiner-','');
		var moduleId = $(this).parents(".amppb-module").attr("id").replace('module-','');
		
		var popupContents = $(this).parents(".amppb-module").find("#selectedModule").val();
		
		$('#ampb-parents-dialog').attr('data-container', conatinerId+'--' +moduleId);
		$('#ampb-parents-dialog').attr('data-modules', popupContents);

		popupContents = JSON.parse(popupContents);
		var popupHtml = '';
		var upload = false; var editor = [];
		var updateSelectValues = [];
		$.each(popupContents.fields, function(fieldsName,fieldReplace){
			var id = fieldReplace.name+"-"+conatinerId+'-' +moduleId;
			var htmlFields = $('.amppb-fields-templates').find("#"+fieldReplace.type).html();
			fieldReplace.default_images = '';
			switch(fieldReplace.type){
				case 'select':
					updateSelectValues.push({id: id, value: decodeURI(fieldReplace.default)});
				break;
				case 'multi_upload':
					var imageHtml = '';
					var imageSrc = fieldReplace.default.split(",");
					$.each(imageSrc, function(id, src){
						if(src!=''){
							imageHtml += '<img src="'+src+'" width="40" height="40">';
						}
					})
					fieldReplace.default_images = imageHtml;
				break;
			}
			
			popupHtml += htmlFields.replace(/{name}/g,fieldReplace.name).replace(/{label}/g,fieldReplace.label).replace(/{id}/g,id).replace(/{default_value}/g, decodeURI(fieldReplace.default)).replace(/{options}/g, decodeURI(fieldReplace.options)).replace(/{default_images}/g, decodeURI(fieldReplace.default_images));
			
			//To load action of fields
			switch(fieldReplace.type){
				case 'upload':
					upload = true;
					break;
				case 'text-editor':
					editor.push(id);
					//loadEditor(id);
					
					break; 
			}
		});
		$(".amp-pb-module-content").html(popupHtml); 
		if(updateSelectValues.length>0){
			$.each(updateSelectValues, function(id, v){
				$("#"+ v.id).val(v.value);
			})
		}
		editorJs(editor);
	});
	
	function editorJs(editor){
		if(editor.length){  
			$.each(editor, function(key, value){
				var control = $("#"+value).parents('form-control'), restoreTextMode = false,triggerChangeIfDirty,changeDebounceDelay = 1000, needsTextareaChangeTrigger;
				var component = {
					dismissedPointers: []
				};
				var textarea = control.find('textarea');
				var id = value;
				if ( typeof window.tinymce === 'undefined' ) {
					wp.editor.initialize( id, {
						quicktags: true
					});
					return;
				}
				// Destroy any existing editor so that it can be re-initialized after a widget-updated event.
				if ( tinymce.get( id ) ) {
					restoreTextMode = tinymce.get( id ).isHidden();
					wp.editor.remove( id );
				}
				wp.editor.initialize( id, {
					tinymce: {
						wpautop: true
					},
					quicktags: true
				});
				triggerChangeIfDirty = function() {
				var updateWidgetBuffer = 300; 
				if ( control.editor.isDirty() ) {
					if ( wp.customize && wp.customize.state ) {
						wp.customize.state( 'processing' ).set( wp.customize.state( 'processing' ).get() + 1 );
						_.delay( function() {
							wp.customize.state( 'processing' ).set( wp.customize.state( 'processing' ).get() - 1 );
						}, updateWidgetBuffer );
					}

					if ( ! control.editor.isHidden() ) {
						control.editor.save();
					}
				}
				// Trigger change on textarea when it is dirty for sake of widgets in the Customizer needing to sync form inputs to setting models.
				if ( needsTextareaChangeTrigger ) {
					textarea.trigger( 'change' );
					needsTextareaChangeTrigger = false;
				}
			};
				control.customHtmlWidgetPointer = control.find( '.wp-pointer.custom-html-widget-pointer' );
				if ( control.customHtmlWidgetPointer.length ) {
					control.customHtmlWidgetPointer.find( '.close' ).on( 'click', function( event ) {
						event.preventDefault();
						control.customHtmlWidgetPointer.hide();
						$( '#' + control.fields.text.attr( 'id' ) + '-html' ).focus();
						control.dismissPointers( [ 'text_widget_custom_html' ] );
					});
					control.customHtmlWidgetPointer.find( '.add-widget' ).on( 'click', function( event ) {
						event.preventDefault();
						control.customHtmlWidgetPointer.hide();
						control.openAvailableWidgetsPanel();
					});
				}
				control.pasteHtmlPointer = control.find( '.wp-pointer.paste-html-pointer' );
				if ( control.pasteHtmlPointer.length ) {
					control.pasteHtmlPointer.find( '.close' ).on( 'click', function( event ) {
						event.preventDefault();
						control.pasteHtmlPointer.hide();
						control.editor.focus();
						control.dismissPointers( [ 'text_widget_custom_html', 'text_widget_paste_html' ] );
					});
				}
				/**
				 * Show a pointer, focus on dismiss, and speak the contents for a11y.
				 *
				 * @param {jQuery} pointerElement Pointer element.
				 * @returns {void}
				 */
				showPointerElement = function( pointerElement ) {
					pointerElement.show();
					pointerElement.find( '.close' ).focus();
					wp.a11y.speak( pointerElement.find( 'h3, p' ).map( function() {
						return $( this ).text();
					} ).get().join( '\n\n' ) );
				};

				editor = window.tinymce.get( id );
				if ( ! editor ) {
					throw new Error( 'Failed to initialize editor' );
				}
				onInit = function() {

					// If a prior mce instance was replaced, and it was in text mode, toggle to text mode.
					if ( restoreTextMode ) {
						switchEditors.go( id, 'html' );
					}

					// Show the pointer.
					$( '#' + id + '-html' ).on( 'click', function() {
						//control.pasteHtmlPointer.hide(); // Hide the HTML pasting pointer.

						if ( -1 !== component.dismissedPointers.indexOf( 'text_widget_custom_html' ) ) {
							return;
						}
						showPointerElement( control.customHtmlWidgetPointer );
					});

					// Hide the pointer when switching tabs.
					$( '#' + id + '-tmce' ).on( 'click', function() {
						control.customHtmlWidgetPointer.hide();
					});

					// Show pointer when pasting HTML.
					editor.on( 'pastepreprocess', function( event ) {
						var content = event.content;
						if ( -1 !== component.dismissedPointers.indexOf( 'text_widget_paste_html' ) || ! content || ! /&lt;\w+.*?&gt;/.test( content ) ) {
							return;
						}

						// Show the pointer after a slight delay so the user sees what they pasted.
						_.delay( function() {
							showPointerElement( control.pasteHtmlPointer );
						}, 250 );
					});
				};

				if ( editor.initialized ) {
					onInit();
				} else {
					editor.on( 'init', onInit );
				}

				control.editorFocused = false;

				editor.on( 'focus', function onEditorFocus() {
					control.editorFocused = true;
				});
				editor.on( 'paste', function onEditorPaste() {
					editor.setDirty( true ); // Because pasting doesn't currently set the dirty state.
					triggerChangeIfDirty();
				});
				editor.on( 'NodeChange', function onNodeChange() {
					needsTextareaChangeTrigger = true;
				});
				editor.on( 'NodeChange', _.debounce( triggerChangeIfDirty, changeDebounceDelay ) );
				editor.on( 'blur hide', function onEditorBlur() {
					control.editorFocused = false;
					triggerChangeIfDirty();
				});
				control.editor = editor;
				 
			})
			
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
    var moduleLoad = function(){
    	$('.amppb-row-fields .col:not(:last-child)').resizable({
		   resizeHeight: false,
		   handles : 'e',
		   start : function(){
		   	   tot_width = jQuery(this).width() + jQuery(this).next().width();
		   	   jQuery(this).resizable('option', 'maxWidth', ((tot_width /(100-30))*100));
		   },
		   resize : function(){
		    jQuery(this).next().width(tot_width - jQuery(this).width());
		   }
		});
		
		//Module Droppable Container
		$( ".modules-drop" ).droppable({
			accept:'.amppb-module-actions > span,.amppb-module',
			revert:true,
			hoverClass: "hover",
			tolerance: "touch",
			classes: {
				"ui-droppable-active": "ui-state-active",
				"ui-droppable-hover": "ui-state-hover"
			  },
			drop: function( event, ui ) {
				ui.draggable.attr('style','').context;
				var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
				var containerId = $(this).parents('.amppb-row').attr('id').replace('conatiner-','');
				var cellContainerNo = $(this).parents('div.col').attr('data-cell');

				//when exchange the modules
				if(ui.draggable.hasClass('module-draggable')){
					
					$( this ).append(ui.draggable.context);

					var moduleId = ui.draggable.attr('id').replace("module-","");
					var previousValue = {};
					$.each(ploatedStructure.rows,function(k,rowVal){
						if(rowVal.cell_data.length>0){
							$.each(rowVal.cell_data,function(key,columnVal){
								if(moduleId==columnVal.cell_id){
									previousValue = ploatedStructure.rows[k].cell_data[key];
									ploatedStructure.rows[k].cell_data.splice(key, 1);
									return false;
								}
							});
							console.log(previousValue);
						}
					});
					$.each(ploatedStructure.rows,function(k,rowVal){
						if(containerId==rowVal.id){
							previousValue.container_id = containerId;
							previousValue.cell_container = cellContainerNo;
							ploatedStructure.rows[k].cell_data.push(previousValue);
						}
					});
					storeJsonDataInput(ploatedStructure);

				}else{ //when 

					$moduleId = parseInt($('#amppb-module-actions-container').attr('data-recentid'));
					$('#amppb-module-actions-container').attr('data-recentid',$moduleId+1);

					var currentDropZone = this;
					var templateclass = ui.draggable.attr("data-template");
					var template = '.amppb-module-templates > .amppb-module-' + templateclass;
					$( template ).clone().attr('id','module-'+$moduleId).appendTo(this);
					
					var moduleJson = JSON.parse($(this).find('div.amppb-module:last').find("#selectedModule").val());
					//Store module inside the array
					$.each(ploatedStructure.rows,function(k,columnVal){
						console.log(columnVal.id+' '+containerId);
						if(columnVal.id==containerId){
							
							var moduleIndex = $(currentDropZone).find('div.amppb-module').length;
							var cellData = {
									'cell_id'	: $moduleId,
									'index'		: moduleIndex,
									'type'		: templateclass,
									'container_id': containerId,
									'cell_container': cellContainerNo,
									};

								if(moduleJson.fields.length > 0){
									$.each(moduleJson.fields, function(key, moduleData){
										cellData[moduleData.name] = moduleData.default;
									});
								}
								ploatedStructure.rows[k].cell_data.push(cellData);

							
						}
					});
					ploatedStructure['totalmodules'] = $moduleId+1;
					storeJsonDataInput(ploatedStructure);
					console.log(ploatedStructure);
					
				}
				loadAfterModule();
				//Add new  class if module present
				$(this).addClass("has-module");
				callToRemoveHasModule();
			}
		});//Module Dropable closed
		
		
	}
	
	var loadAfterModule = function(){
		
		 $( ".modules-drop" ).sortable({
			placeholder: "ui-state-highlight-module",
			cursor: "move",
			//handle  : '.move-modules',
			connectWith: ".modules-drop",
      		stack: '.amppb-module',
			forcePlaceholderSize: true,
             activate: function(e, ui){
				ui.helper.width(40); 
				ui.item.height(40);
                 
             },
			 over: function(e, ui){
				ui.item.width(40); 
				ui.item.height(40);
			}, 
//			cursorAt : { left: 45,top:45 },
			start: function(event, ui ){
				//$(ui.item).addClass("module-dragging");
			},
            stop:function(){
                callToRemoveHasModule();
            },
			beforeStop: function(){
				//$(ui.item).removeClass("module-dragging");
			},
			receive: function(event, ui) {
			  //ui.item.appendTo(".module-draggable");
			},
			update  : function(){
				var ploatedStructure = JSON.parse($('#amp-page-builder-data').val());
				var indexOfRow = $(this).parents('.amppb-row').attr('id').replace("conatiner-","");
				$(this).find('div.amppb-module').each(function(indexKey,val){
					console.log(indexKey);
					var indexOfModule = $(this).attr('id').replace('module-','');
					$.each(ploatedStructure.rows,function(k,columnVal){
						if(columnVal.id==indexOfRow){
							$.each(columnVal.cell_data,function(kModule,moduleVal){
								if(moduleVal.cell_id==indexOfModule){
									moduleVal.index = indexKey+1;
								}
							})
							//columnVal.index = indexKey+1;
						}
					});
				});
				storeJsonDataInput(ploatedStructure)
			}
			
			
		}); 
	}
	

	//Restored Prebvious Data
	if($('#amp-page-builder-data').length > 0){
		var valTemplate = $('#amp-page-builder-data').val(); 
		if(valTemplate!=''){
			
			valTemplate = JSON.parse(valTemplate);
			sampleData = valTemplate;
			if(valTemplate.rows.length>0){
				if($(".amppb-rows").find('.dummy').length>0){
					$(".amppb-rows").find('.dummy').html("please wait...");
				}
				valTemplate.rows.sort(function(a, b){
					var a1= a.index, b1= b.index;
					if(a1== b1) return 0;
					return a1> b1? 1: -1;
				});

				$.each(valTemplate.rows, function(val, rowData){
					var templateclass = rowData.cells;
					var containerid = rowData.id;
					var containerIndex = rowData.index;
					var template = '.amppb-templates > .amppb-col-' + templateclass;
					var templateIndex = [];
					$( template ).clone().attr('id','conatiner-'+containerid).appendTo('#sorted_rows');

					rowData.cell_data.sort(function(a, b){
						var a1= a.index, b1= b.index;
						if(a1== b1) return 0;
						return a1> b1? 1: -1;
					});
					if(rowData.cell_data.length>0){
						$.each(rowData.cell_data, function(val, cellData){
							var moduleclass = cellData.type;
							var cellContainerId = cellData.cell_container;
							var template = '.amppb-module-templates > .amppb-module-' + moduleclass;
							//$( template ).clone().attr('id','module-'+$moduleId).appendTo(this);
							if(template.length>0){
								$templteValue = JSON.parse($( template ).find("#selectedModule").val());
								$.each($templteValue.fields,function(key,value){
									value.default = cellData[value.name]
								});

								$("#conatiner-"+containerid).find('div.col[data-cell='+cellContainerId+"]").find("div.modules-drop").append($( template ).clone().attr('id','module-'+cellData.cell_id) );

								$("#conatiner-"+containerid).find('div.col[data-cell='+cellContainerId+"]").find("div.modules-drop").find(".amppb-module:last").find('#selectedModule').val( JSON.stringify($templteValue) );
								//Add new  class if module present
								$("#conatiner-"+containerid).find('div.col[data-cell='+cellContainerId+"]").find("div.modules-drop").addClass("has-module");
							}
							loadAfterModule();
						});
					}


				});
				moduleLoad();
				if($(".amppb-rows").find('.dummy').length>0){
					$(".amppb-rows").find('.dummy').remove();
				}
			}
			
		}//if Checkclosed 
	}//$('#amp-page-builder-data') length checked close


	/***************
	 *
	 * Select Image
	 *
	***************/
		$(document.body).on('click', "input.selectImage", function(e){
			e.preventDefault();
			console.log("selectImage click event called");
			var currentSelectfield = $(this);
			var selectorType = currentSelectfield.attr("data-imageselactor");
			var multiple = false;
			if(selectorType=='multiple'){
				multiple = true;
			}
			var image_frame;
			if(image_frame){
			 image_frame.open();
			}
			// Define image_frame as wp.media object
			image_frame = wp.media({
			           title: 'Select Media',
			           multiple : multiple,
			           library : {
			                type : 'image',
			            }
			       });

			image_frame.on('close',function() {
	                          // On close, get selections and save to the hidden input
	                          // plus other AJAX stuff to refresh the image preview
	                          var selection =  image_frame.state().get('selection');
	                          var gallery_ids = new Array();
	                          var my_index = 0;
	                          selection.each(function(attachment) {
	                             gallery_ids[my_index] = attachment['id'];
	                             my_index++;
	                          });
	                          var ids = gallery_ids.join(",");
	                          currentSelectfield.parents('.form-control').find('input[type=hidden]').val(ids);
	                          Refresh_Image(ids,currentSelectfield);
	                       });
			image_frame.on('open',function() {
	                        // On open, get the id from the hidden input
	                        // and select the appropiate images in the media manager
	                        var selection =  image_frame.state().get('selection');
	                        ids = currentSelectfield.parents('.form-control').find('input[type=hidden]').val().split(',');
	                        ids.forEach(function(id) {
	                          attachment = wp.media.attachment(id);
	                          attachment.fetch();
	                          selection.add( attachment ? [ attachment ] : [] );
	                        });

	                      });
	        image_frame.open();
		});
	

	function loadEditor(id){
		id = id;//.replace("-","_").replace("-","_");
		console.log("loadEditor function called "+ id);
		
		

	}

	function callToRemoveHasModule(){
		console.log("called Function");
		$(".modules-drop").each(function(index, container){
			if($(this).find('.amppb-module').length==0){
				$(this).removeClass("has-module");
			}
		})
	}


});

/**
 *
 *
 * Functions 
 *
 *
 *
 **/
// Ajax request to refresh the image preview
function Refresh_Image(the_id,currentSelectfield){
        var data = {
            action: 'ampforwp_get_image',
            id: the_id
        };

        jQuery.get(ajaxurl, data, function(response) {

            if(response.success === true) {
            	console.log(response.data)
				if(currentSelectfield.attr("data-imageselactor")=='multiple'){
					currentSelectfield.parents('.form-control').find('.sample-gallery-template').html("");
					var imageSrc = '';
					jQuery.each(response.data, function(keys,imageValue){
						console.log(imageValue.image);
						currentSelectfield.parents('.form-control').find('.sample-gallery-template').append(imageValue.image);
						currentSelectfield.parents('.form-control').find('.sample-gallery-template').find('img:last').attr("width",100).attr("height",100);
						imageSrc += imageValue.detail[0]+",";
					});
					currentSelectfield.parents('.form-control').find('input[type=hidden]').val(imageSrc)
				}else{
					currentSelectfield.parents('.form-control').find('#ampforwp-preview-image').replaceWith( response.data.image ).attr('id','ampforwp-preview-image');
					var src = currentSelectfield.parents('.form-control').find('#ampforwp-preview-image').attr('src');
					currentSelectfield.parents('.form-control').find('input[type=hidden]').val(src);
				}
            }
        });
}


var sampleData = { 
				'rows':[]
                 }

function storeJsonDataInput(sampleData){
	jQuery('#amp-page-builder-data').val(JSON.stringify(sampleData));
}
jQuery(".customclass_for_addattr").attr('data-bvalidator','required');

