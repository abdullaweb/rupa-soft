$(function(){
    $(document).on('click','#delete',function(e){
        e.preventDefault();
        var link = $(this).attr("href");


                  Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete This Data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = link
                      Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                    }
                  })


    });

    $(document).on('click','#decline',function(e){
      e.preventDefault();
      var link = $(this).attr("href");


                Swal.fire({
                  title: 'Are you sure?',
                  text: "Decline Due Payment?",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, decline it!'
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = link
                    Swal.fire(
                      'Deleted!',
                      'Due has been declined.',
                      'success'
                    )
                  }
                })


  });

  $(document).on('click','#approve',function(e){
    e.preventDefault();
    var link = $(this).attr("href");


              Swal.fire({
                title: 'Are you sure?',
                text: "Approve Due Payment?",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = link
                  Swal.fire(
                    'Approved!',
                    'Due has been Aprroved.',
                    'success'
                  )
                }
              })


});

  });
