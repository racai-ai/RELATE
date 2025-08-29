/* nu merge direct in clipboard, e mai ok ctrl+c

var $grid=false;
function gridCopy(){
    var $grid = $(this).closest(".pq-grid");
    $grid.pqGrid("copy");
}*/
function loadData(data,func,error){
    loadDataComplete("index.php","POST",data,func,error);
}

function loadDataComplete(url,method,data,func,error){
    var xhttp = false;
    
    if (window.XMLHttpRequest) {
        // code for modern browsers
        xhttp = new XMLHttpRequest();
    } else {
        // code for old IE browsers
        xhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4){
        if(this.status == 200) {
          var response=this.responseText;
          func(response);
        }else{
          if(error!==undefined && error!==null)
            error();
        }
      }
    };
    if(error!==undefined && error!==null)
      xhttp.error=error;    
      
    xhttp.open(method, url, true);
    if(data!==undefined && data!==null){
    		if(!(data instanceof FormData)){
        		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        xhttp.send(data);
    }else{
        xhttp.send();
    }
}


var $grid=false;

function gridAdd(){

            var $frm = $("form#crud-form");
            $frm.find("input").val("");

            $("#popup-dialog-crud").dialog({ title: "Add Holiday", buttons: {
                Add: function () {
                    var data = new FormData();
                    data.append('path', 'projects/holidays_add');
                    data.append('date', $frm.find("input[name='date']").val());
                    data.append('description', $frm.find("input[name='description']").val());
                    var dia=$(this);
                    loadData(data,function(d){
                        $grid.pqGrid('refreshDataAndView');
                        dia.dialog("close");
                    }, function(){ alert("Error adding new holiday"); });
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
            });
            $("#popup-dialog-crud").dialog("open");
} 

function gridEdit(){
}

function gridDelete(){
}
 
 
$(document).ready(function () {
      
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                { type: 'button', label: 'Add', listeners: [{ click: gridAdd}], icon: 'ui-icon-plus' },
                //{ type: 'button', label: 'Edit', listeners: [{ click: gridEdit}], icon: 'ui-icon-pencil' },
                //{ type: 'button', label: 'Delete', listeners: [{ click: gridDelete}], icon: 'ui-icon-minus' }                
            ]
        };        

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "Holidays"
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
                //window.location.href="index.php?path=projects/person&name="+ui.rowData.name;
            }            
        };
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Date", dataType: "string", dataIndx: "date", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Description", dataType: "string", dataIndx: "description", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "date",
            sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=projects/holidays_get",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $grid = $("#grid").pqGrid(obj);

          $("#popup-dialog-crud").dialog({ width: 600, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
    });
