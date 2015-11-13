        $(function() {
        $('#change_score').change( function () {
//              location.href = 'aggregate_view.php?id='+<?php echo $id;?>;             
                var change_score_value=$("#change_score").attr("value");
        switch (change_score_value)
        {
                case 'qualifications':
                        location.href = 'view.php?id=' + id;       
                  break;
                case 'result':
                        location.href = 'aggregate_view.php?id=' + id;
                  break;
                default:
        }
        });

                $('#downloadscormtalbe').click( function () {
                        setTimeout( location.href=(odsURL + '?id=' + id), 50); //ms    
                });

        });
