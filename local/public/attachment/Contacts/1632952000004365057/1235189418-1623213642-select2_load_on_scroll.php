<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


  <!-- Select2 cdn load option data when scroll -->

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

  <!-- Select2 cdn load option data when scroll End -->

  
</head>
<body>
  <div class="container">
    <!-- Select2 load option data when scroll -->

  <select class="js-data-example-ajax col-xs-9"></select>
  
   <!-- Select2 load option data when scroll End -->

  </div>

  <!-- Select2 for get controler data -->
<!-- ---------------------------------------- -->
      <!-- Important -->
<!-- --------------------------------------------- -->
<!-- page value get in controller by $_GET['page'] 
     search value get in controller by $_GET['q'] -->

<script type="text/javascript">
      // $(document).ready(function() {

      //   $(".js-data-example-ajax").select2({

      //     var total_count = $('#total_count').val(); //get total row in hidden field from controller when page first load


      //     ajax: {
      //       url: "<?php //echo base_url();?>Contacts/get_all_product",
      //       dataType: 'json',
      //       delay: 250,
      //       data: function (params) {
      //         return {
      //           //q: params.term, // search term
      //           page: params.page
      //         };
      //       },
      //       processResults: function (data, params) {

      //           //console.log(data);
                
      //         params.page = params.page || 1;
      //         console.log(params);

      //         return {
      //           results: data,
      //           pagination: {
      //             more: (params.page * 20) < data.length //total_count
      //           }
      //         };
      //       },
      //       cache: true
      //     },
      //     placeholder: 'Search for a repository',
      //     escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
      //     //minimumInputLength: 1,
      //     templateResult: formatRepo,
      //     templateSelection: formatRepoSelection
      //   });

      //   function formatRepo (repo) {
      //     if (repo.loading) {
      //       return repo.text;
      //     }

      //     var markup = "<option value='" + repo.id + "'>" + repo.field_1 + "</option>"; //id is crmid...this is renamed from controller db query when get data by ajax

         

      //     return markup;
      //   }

      //   function formatRepoSelection (repo) {
      //     return repo.field_1 || repo.text;
      //   }
      // });
    </script>


  </body>
</html>

<!-- See below for get data from controller source -->
    <script type="text/javascript">
      $(document).ready(function() {

        $(".js-data-example-ajax").select2({
          ajax: {
            url: "https://api.github.com/search/repositories",
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page
              };
            },
            processResults: function (data, params) {
              // parse the results into the format expected by Select2
              // since we are using custom formatting functions we do not need to
              // alter the remote JSON data, except to indicate that infinite
              // scrolling can be used
              params.page = params.page || 1;

              return {
                results: data.items,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
          },
          placeholder: 'Search for a repository',
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 1,
          templateResult: formatRepo,
          templateSelection: formatRepoSelection
        });

        function formatRepo (repo) {
          if (repo.loading) {
            return repo.text;
          }

          var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
            "<div class='select2-result-repository__meta'>" +
              "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";

          if (repo.description) {
            markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
          }

          markup += "<div class='select2-result-repository__statistics'>" +
            "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
            "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
            "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
          "</div>" +
          "</div></div>";

          return markup;
        }

        function formatRepoSelection (repo) {
          return repo.full_name || repo.text;
        }
      });
    </script>