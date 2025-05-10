import DataTable from 'datatables.net-dt';
import Responsive from 'datatables.net-responsive-dt';

DataTable.Responsive = Responsive;
import 'datatables.net-dt/css/dataTables.dataTables.min.css';

let table = new DataTable('#mytable', {
    responsive: true
});

