$(document).ready(function(){
    $('#datatable_init_expense').DataTable({
        paging:true,
        ordering:true,
        info: true,
        "footerCallback": function(row, data){
            var total = 0;
            console.log(data);
            var api = this.api(), data;
            
            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\AED,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            var pageTotal = api
                    .column(4, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

            $( api.column(3).footer() ).html('AED '+ pageTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            
            // alert(pageTotal);k
            
        }
        });

        $('#from_date_expense, #to_date_expense').on('change',function(){
            // DataTables initialisation
            var table = $('#datatable_init_expense').DataTable();
            // Refilter the table
            table.draw();
        });

        $.fn.dataTable.ext.search.push(
        function( settings, data, dataIndex ) {
            var from_date = $('#from_date_expense').val();
            var to_date = $('#to_date_expense').val();
            var date = new Date(data[5]).getDate();
            var month = new Date(data[5]).getMonth() + 1;
            var year = new Date(data[5]).getFullYear();

            if(month <= 9){
                month = '0'+month;
            }

            var full_date = year+'-'+month+'-'+date;
    
            
            if (full_date >= from_date && full_date <= to_date) 
            {
                return true;
            }
            return false;
        });

        //***********************************************//
});