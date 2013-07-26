$(document).ready(function() {
	/*
	 * Function: fnGetColumnData
	 * Purpose:  Return an array of table values from a particular column.
	 * Returns:  array string: 1d data array
	 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
	 *           int:iColumn - the id of the column to extract the data from
	 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
	 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
	 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
	 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
	 */
	$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
	    // check that we have a column id
	    if ( typeof iColumn == "undefined" ) return new Array();
	     
	    // by default we only want unique data
	    if ( typeof bUnique == "undefined" ) bUnique = true;
	     
	    // by default we do want to only look at filtered data
	    if ( typeof bFiltered == "undefined" ) bFiltered = true;
	     
	    // by default we do not want to include empty values
	    if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
	     
	    // list of rows which we're going to loop through
	    var aiRows;
	     
	    // use only filtered rows
	    if (bFiltered == true) aiRows = oSettings.aiDisplay;
	    // use all rows
	    else aiRows = oSettings.aiDisplayMaster; // all row numbers
	 
	    // set up data array   
	    var asResultData = new Array();
	     
	    for (var i=0,c=aiRows.length; i<c; i++) {
	        iRow = aiRows[i];
	        var aData = this.fnGetData(iRow);
	        var sValue = aData[iColumn];
	         
	        // ignore empty values?
	        if (bIgnoreEmpty == true && sValue.length == 0) continue;
	 
	        // ignore unique values?
	        else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
	         
	        // else push the value onto the result data array
	        else asResultData.push(sValue);
	    }
	     
	    return asResultData;
	};
	
    $.fn.dataTableExt.oApi.fnFilterOnReturn = function (oSettings) {
        var _that = this;
      
        this.each(function (i) {
            $.fn.dataTableExt.iApiIndex = i;
            var $this = this;
            var anControl = $('input', _that.fnSettings().aanFeatures.f);
            anControl.unbind('keyup').bind('keypress', function (e) {
                if (e.which == 13) {
                    $.fn.dataTableExt.iApiIndex = i;
                    var tablename = $('#cont' + _that.fnSettings().sTableId).find('input[name="tablename"]').val();
                    Update_Table(0,tablename,anControl.val())
                }
            });
            return this;
        });
        return this;
    };
    $.fn.dataTableExt.oPagination.four = {
        /*
         * Function: oPagination.four_button.fnInit
         * Purpose:  Initalise dom elements required for pagination with a list of the pages
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           node:nPaging - the DIV which contains this pagination control
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        "fnInit" : function(oSettings, nPaging, fnCallbackDraw) {
            nFirst = document.createElement('span');
            nPrevious = document.createElement('span');
            nActual = document.createElement('span');
            nNext = document.createElement('span');
            nLast = document.createElement('span');

            nFirst.className = "paginate_button first";
            nPrevious.className = "paginate_button previous";
            nNext.className = "paginate_button next";
            nLast.className = "paginate_button last";

            var reg_enc   = $('#cont' + oSettings.sTableId).find('input[name="reg_enc"]').val();
            var pages     = Math.ceil(reg_enc/oSettings._iDisplayLength);
            var tablename = $('#cont' + oSettings.sTableId).find('input[name="tablename"]').val();
            var buscar = $('input', oSettings.aanFeatures.f).val();
            
            $(nFirst).html('<i class="icon-backward" title="' + oSettings.oLanguage.oPaginate.sFirst + '"></i>');
            $(nPrevious).html('<i class="icon-step-backward" title="' + oSettings.oLanguage.oPaginate.sFirst + '"></i>');
            $(nNext).html('<i class="icon-step-forward" title="' + oSettings.oLanguage.oPaginate.sFirst + '"></i>');
            $(nLast).html('<i class="icon-forward" title="' + oSettings.oLanguage.oPaginate.sFirst + '"></i>');
            $(nActual).html('Page <input type="text" class="table_pager" name="actpage" value="1"> of <span id="total_pages'+oSettings.sTableId+'">' + pages +'</span>');
            nPaging.appendChild(nFirst);
            nPaging.appendChild(nPrevious);
            nPaging.appendChild(nActual);
            nPaging.appendChild(nNext);
            nPaging.appendChild(nLast);

            $(nActual).find('input[name="actpage"]').keypress(function(event){
                if(event.which == 13){
                    Update_Table($(this).attr('value'),tablename,buscar);
                }
            });
            $(nFirst).click(function() {
                var buscar = $('input', oSettings.aanFeatures.f).val();
                Update_Table(1,tablename,buscar);
            });
            $(nPrevious).click(function() {
                var buscar = $('input', oSettings.aanFeatures.f).val();
                var actual = $(this).closest('.tablecontainer').find('input[name="actpage"]').val();
                Update_Table(actual>1?(parseInt(actual)-1):1,tablename,buscar);
            });
            $(nNext).click(function() {
                var buscar = $('input', oSettings.aanFeatures.f).val();
                var actual = $(this).closest('.tablecontainer').find('input[name="actpage"]').val();
                Update_Table(actual<pages?(parseInt(actual)+1):pages,tablename,buscar);
            });
            $(nLast).click(function() {
                var buscar = $('input', oSettings.aanFeatures.f).val();
                Update_Table(pages,tablename,buscar);
            });
            /* Disallow text selection */
            $(nFirst).bind('selectstart', function() {
                return false;
            });
            $(nPrevious).bind('selectstart', function() {
                return false;
            });
            $(nNext).bind('selectstart', function() {
                return false;
            });
            $(nLast).bind('selectstart', function() {
                return false;
            });
        },

        /*
         * Function: oPagination.four_button.fnUpdate
         * Purpose:  Update the list of page buttons shows
         * Returns:  -
         * Inputs:   object:oSettings - dataTables settings object
         *           function:fnCallbackDraw - draw function which must be called on update
         */
        "fnUpdate" : function(oSettings, fnCallbackDraw) {
            if (!oSettings.aanFeatures.p) {
                return;
            }
            page = document.getElementById('actpage');
            $(page).val(Math.ceil(oSettings.fnDisplayEnd() / oSettings._iDisplayLength));
            /* Loop over each instance of the pager */
            var an = oSettings.aanFeatures.p;
            for ( var i = 0, iLen = an.length; i < iLen; i++) {
                var buttons = an[i].getElementsByTagName('span');
                if (oSettings._iDisplayStart === 0) {
                    buttons[0].className = "paginate_disabled_previous";
                    buttons[1].className = "paginate_disabled_previous";
                } else {
                    buttons[0].className = "paginate_enabled_previous";
                    buttons[1].className = "paginate_enabled_previous";
                }

                if (oSettings.fnDisplayEnd() == oSettings.fnRecordsDisplay()) {
                    buttons[2].className = "paginate_disabled_next";
                    buttons[3].className = "paginate_disabled_next";
                } else {
                    buttons[2].className = "paginate_enabled_next";
                    buttons[3].className = "paginate_enabled_next";
                }
            }
        }
    };
});
function Update_Table(pageto,tablename,search){
    var addurl='';
    if(pageto>0) 
        addurl+='/act_page/'+pageto;
    if(search.replace(/\n/g," ").replace( /<.*?>/g, "" ).length>0)
        addurl+='/search/'+search.replace(/\n/g," ").replace( /<.*?>/g, "" );
    $.ajax({
        type:"GET",
        url:RUTA_RAIZ+"admin/"+tablename+'/ajax'+addurl,
        dataType: 'json',
        error:function (jqXHR, textStatus, errorThrown){
            console.log(JSON.stringify(jqXHR) + ' ' + textStatus +'  '+errorThrown );
         }
    }).done(function(data) {
        $('#conttable_'+data.tabla).find('input[name="actpage"]').val(data.page);
        datatable.fnClearTable();
        $('#table_'+data.tabla).find('tbody').html(data.html);
        if(data.reg_enc){
            $('#conttable_' + data.tabla).find('input[name="reg_enc"]').val(data.reg_enc);
            var pages     = Math.ceil(data.reg_enc/datatable.fnSettings()._iDisplayLength);
            $('#conttable_' + data.tabla).find('span#total_pagestable_'+data.tabla).html(pages);
        }
    });
}

function fnCreateSelect( aData ,id,selectlabel)
{
    var r='<select id="'+id+'"><option>'+selectlabel+'</option>', i, iLen=aData.length;
    for ( i=0 ; i<iLen ; i++ )
    {
        r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
    }
    return r+'</select>';
}