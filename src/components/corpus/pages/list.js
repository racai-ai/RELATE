/* nu merge direct in clipboard, e mai ok ctrl+c
function gridCopy(){
    var $grid = $(this).closest(".pq-grid");
    $grid.pqGrid("copy");
}*/

function gridAdd(){

            var $frm = $("form#crud-form");
            $frm.find("input").val("");

            $("#popup-dialog-crud").dialog({ title: "Add Corpus", buttons: {
                Add: function () {
                    var data={
                        name:$frm.find("input[name='name']").val(),
                        lang:$frm.find("select[name='lang']").val(),
                        desc:$frm.find("textarea[name='desc']").val(),
                        hasaudio:$frm.find("input[name='hasaudio']").is(":checked")?("yes"):("no"),
                        hasimage:$frm.find("input[name='hasimage']").is(":checked")?("yes"):("no"),
                        hasvideo:$frm.find("input[name='hasvideo']").is(":checked")?("yes"):("no"),
                        hasgold:$frm.find("input[name='hasgold']").is(":checked")?("yes"):("no"),
                        hasbrat:$frm.find("input[name='hasbrat']").is(":checked")?("yes"):("no"),
                        hasclassification:$frm.find("input[name='hasclassification']").is(":checked")?("yes"):("no"),
                        hascorrected:$frm.find("input[name='hascorrected']").is(":checked")?("yes"):("no"),
                    };
                    
                    var dia=$(this);
                    
                    $.ajax({
                        cache:false,
                        url:"index.php?path=corpus/list_add",
                        data:{data:JSON.stringify(data)},
                        method:"POST"
                    }).done(function(data) {
                        data=JSON.parse(data);
                            if(data.status==false)
                              alert("Cannot add corpus! ["+data.reason+"]");
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
}

function gridDelete(){
}
 
 var $grid=false;
 
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
            , title: "Corpora list"
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
                window.location.href="index.php?path=corpus/corpus&name="+ui.rowData.name;
            }            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };
        obj.colModel = [
            { title: "Name", dataType: "string", dataIndx: "name", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Lang", dataType: "string", dataIndx: "lang", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "User", dataType: "string", dataIndx: "created_by", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } },
            { title: "Description", dataType: "string", dataIndx: "desc", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] }},
            { title: "Creation Date", dataType: "string", dataIndx: "created_date", filter: { type: 'textbox', condition: 'contain', listeners: ['keyup'] } }
        ];
        obj.dataModel = {
            location: "remote",
            sorting: "local",
            sortIndx: "created_date",
            sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/list_get",
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
