/* nu merge direct in clipboard, e mai ok ctrl+c
function gridCopy(){
    var $grid = $(this).closest(".pq-grid");
    $grid.pqGrid("copy");
}*/

function gridAdd(){

	var $frm = $("form#crud-form");
	$frm.find("input").val("");

	$("#popup-dialog-crud").dialog({ title: "Add User", buttons: {
		Add: function () {
			var data={
				name:$frm.find("input[name='name']").val(),
				username:$frm.find("input[name='username']").val(),
				password:$frm.find("input[name='password']").val(),
				password2:$frm.find("input[name='password2']").val(),
				rights:$frm.find("input[name='rights']").val(),
				
			};
			
			var dia=$(this);
			
			$.ajax({
				cache:false,
				url:"index.php?path=admin/users_add",
				data:{data:JSON.stringify(data)},
				method:"POST"
			}).done(function(data) {
				data=JSON.parse(data);
					if(data.status==false)
					  alert("Cannot add user! ["+data.reason+"]");
					$grid.pqGrid('refreshDataAndView');
					dia.dialog("close");
			});
		},
		Cancel: function () {
			$(this).dialog("close");
		}
	}
	});
	$("#popup-dialog-crud").dialog("open");
} 

function gridEdit(){

	var data=$grid.pqGrid("selection", {type:'row', method:'getSelection'});
	if(data===undefined || data[0]===undefined){alert("Select a user to edit");return ;}
	data=data[0].rowData;;

	var $frm = $("form#crud-form-edit");
	$frm.find("input").val("");
	$frm.find("input[name='name']").val(data['name']);
	$frm.find("input[name='username']").val(data['username']);
	$frm.find("input[name='rights']").val(data['rights']);

	$("#popup-dialog-crud-edit").dialog({ title: "Edit User", buttons: {
		Save: function () {
			var data={
				name:$frm.find("input[name='name']").val(),
				username:$frm.find("input[name='username']").val(),
				password:$frm.find("input[name='password']").val(),
				password2:$frm.find("input[name='password2']").val(),
				rights:$frm.find("input[name='rights']").val(),
				edit:true
			};
			
			var dia=$(this);
			
			$.ajax({
				cache:false,
				url:"index.php?path=admin/users_add",
				data:{data:JSON.stringify(data)},
				method:"POST"
			}).done(function(data) {
				data=JSON.parse(data);
					if(data.status==false)
					  alert("Cannot edit user! ["+data.reason+"]");
					$grid.pqGrid('refreshDataAndView');
					dia.dialog("close");
			});
		},
		Cancel: function () {
			$(this).dialog("close");
		}
	}
	});
	$("#popup-dialog-crud-edit").dialog("open");
}

function gridDelete(){
}
 
 var $grid=false;
 
$(document).ready(function () {
      
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add', listeners: [{ click: gridAdd}], icon: 'ui-icon-plus' },
                { type: 'button', label: 'Edit', listeners: [{ click: gridEdit}], icon: 'ui-icon-pencil' },
                //{ type: 'button', label: 'Delete', listeners: [{ click: gridDelete}], icon: 'ui-icon-minus' }                
            ]
        };        

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Users list"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            , scrollModel: { autoFit: true }
            , toolbar: toolbar
            , editable: false
            , selectionModel: { mode: 'single', type: 'row' }
			, filterModel: { on: true, mode: "AND", header: true, type: "local" } 
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: false, hwrap: false
            
            , rowDblClick: function( event, ui ) {
				gridEdit();
                //window.location.href="index.php?path=corpus/corpus&name="+ui.rowData.name;
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Username", dataType: "string", dataIndx: "username", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Rights", dataType: "string", dataIndx: "rights", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }  },
            { title: "Creation Date", dataType: "string", dataIndx: "created_time"}
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "username",
            sortDir: "up",
            dataType:"json",
            method:"GET",
            url:"index.php?path=admin/users_get",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $grid = $("#grid").pqGrid(obj);

        $("#popup-dialog-crud").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });

        $("#popup-dialog-crud-edit").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });


    });
