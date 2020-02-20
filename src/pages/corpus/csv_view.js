/* nu merge direct in clipboard, e mai ok ctrl+c
function gridCopy(){
    var $grid = $(this).closest(".pq-grid");
    $grid.pqGrid("copy");
}*/

 
 var $grid=false;
 
$(document).ready(function () {
      
        var toolbar = { items: [
                //{ type: 'button', label: 'Copy', listeners: [{ click: gridCopy }] }, 
                
                //{ type: 'button', label: 'Add', listeners: [{ click: gridAdd}], icon: 'ui-icon-plus' },
                //{ type: 'button', label: 'Edit', listeners: [{ click: gridEdit}], icon: 'ui-icon-pencil' },
                //{ type: 'button', label: 'Delete', listeners: [{ click: gridDelete}], icon: 'ui-icon-minus' }                
            ]
        };        

        var obj = {
            width: "100%"
            , height: 400
            , resizable: true
            , title: "File View"
            , showBottom: false
            , editModel: {clicksToEdit: 2}
            //, scrollModel: { autoFit: true }
            //, toolbar: toolbar
            , editable: false
            , resizable: true
            , selectionModel: { mode: 'single', type: 'row' }
            
            , pageModel: { type: "local", rPP: 20, strRpp: "{0}", strDisplay: "{0} to {1} of {2}" }
            ,  wrap: true, hwrap: false
            
            //, rowDblClick: function( event, ui ) {
            //    window.location.href="index.php?path=corpus/corpus&name="+ui.rowData.name;
            //}            
        };
        function formatCurrency(ui) {
            return ((ui.cellData < 0) ? "-" : "") + "$" + $.paramquery.formatCurrency(ui.cellData);
        }
        obj.columnTemplate = { minWidth: '10%', maxWidth: '80%' };

        var url = new URL(window.location);
        var type = url.searchParams.get("type");
        if(type==="conllu"){
            obj.colModel = [
                    { title: "ID", dataType: "string", dataIndx: "0" },
                    { title: "Form", dataType: "string", dataIndx: "1" },
                    { title: "Lemma", dataType: "string", dataIndx: "2" },
                    { title: "UPOS", dataType: "string", dataIndx: "3" },
                    { title: "XPOS", dataType: "string", dataIndx: "4" },
                    { title: "Feats", dataType: "string", dataIndx: "5" },
                    { title: "Head", dataType: "string", dataIndx: "6" },
                    { title: "Deprel", dataType: "string", dataIndx: "7" },
                    { title: "Deps", dataType: "string", dataIndx: "8" },
                    { title: "Misc", dataType: "string", dataIndx: "9" },
                    { title: "NER", dataType: "string", dataIndx: "10" },
                    { title: "NP", dataType: "string", dataIndx: "11" },
                    { title: "IATE", dataType: "string", dataIndx: "12" },
                    { title: "EUROVOC", dataType: "string", dataIndx: "13" }
                ];
        }else if(type==="csv2"){
            obj.colModel = [
                { title: "0", dataType: "string", dataIndx: "0" },
                { title: "1", dataType: "string", dataIndx: "1" },
            ];
        
        }else{
            obj.colModel = [
                { title: "C0", dataType: "string", dataIndx: "0" },
                { title: "C1", dataType: "string", dataIndx: "1" },
                { title: "C2", dataType: "string", dataIndx: "2" }
            ];
        }
        obj.dataModel = {
            location: "remote",
            //sorting: "local",
            //sortIndx: "name",
            //sortDir: "down",
            dataType:"json",
            method:"GET",
            url:"index.php?path=corpus/csv_get&corpus={{CORPUS_NAME}}&file={{CORPUS_FILE}}",
            getData: function (dataJSON) {
                return { data: dataJSON };
            }
        };
        
        $grid = $("#grid").pqGrid(obj);

          $("#popup-dialog-crud").dialog({ width: 500, modal: true,
            open: function () { $(".ui-dialog").position({ of: "#grid" }); },
            autoOpen: false
        });
    });
