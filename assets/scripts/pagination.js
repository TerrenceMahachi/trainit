class PageManager {
  constructor(tb) {
    this.viewMode = localStorage.getItem('viewMode') || 'table';
    this.tableUri = site_url + 'api/' + tb;
    this.paginationData = {};
    this.table = tb;
    this.table_columns = [];
    this.display_order = '';
    this.page_size = 25;
    this.text_filter = '';
    this.list_filters = {};
    this.current_page = 1;
    this.total_records = 0;
    this.last_record = {};
    this.records = [];
    this.record = {};

    this.init();
  }

  init() {
    this.setViewMode();
    this.setSelects();
    this.bindEvents();
    this.get_pagination();
    console.log(this.tableUri )
  }

  setViewMode() {
    if (this.viewMode === 'table') {
      $('#content').addClass('tableView').removeClass('listView');
      $('#viewToggle .tableView').addClass('selected');
      $('#viewToggle .listView').removeClass('selected');
    } else {
      $('#content').addClass('listView').removeClass('tableView');
      $('#viewToggle .listView').addClass('selected');
      $('#viewToggle .tableView').removeClass('selected');
    }

  }

  setSelects() {

    $('.f-sel').each(function () {
      //get_item_list('currency', "iD", "name")
      var sel = $(this);
      var tb = sel.attr('data-table')
      var col = sel.attr('data-property')
      var uri = global_uri + "api_get_item_list";
      request(uri, { Table: tb, Column: "name", iD: "iD" })
        .then(response => {
          console.log('POST response:', response);
          sel.html(' <option value="-1">Select ' + col + ' </option> ' + response.list)

        })
        .catch(error => {
          console.error('POST request error:', error);
        });

    })

    $('.s-sel').each(function () {
      //get_item_list('currency', "iD", "name")
      var sel = $(this);
      var tb = sel.attr('data-table')
      var col = sel.attr('data-property')
      var uri = global_uri + "api_get_item_list";
      // console.log('uri: ' + uri + "; tb: " + tb + "; col: " + col)
      request(uri, { Table: tb, Column: "name", iD: "iD" })
        .then(response => {
          console.log('POST response:', response);
          sel.html(' <option value="-1">Select ' + col + ' </option> ' + response.list)

        })
        .catch(error => {
          console.error('POST request error:', error);
        });
    })
  }

  bindEvents() {
    $('#viewToggle span').click((e) => {
      const mode = $(e.target).hasClass('listView') ? 'list' : 'table';
      localStorage.setItem('viewMode', mode);
      this.viewMode = mode;
      this.setViewMode();
      this.get_page(this.current_page);
    });

    $("#btn_previous").click(() => this.previous_page());
    $("#btn_next").click(() => this.next_page());
    $("#current_page").change((e) => this.get_page($(e.target).val()));
    $('input[name="p_size"]').change(() => this.get_pagination());
    $("#btn_search").click(() => this.get_pagination());
    $('.select').change(() => this.get_pagination());
    $('.display_order').change(() => this.get_pagination());
    $("#btn_add_item").on("click", function () {  $('#add_item_modal').modal('show') })
  }

  refresh_pagination() {
    this.get_pagination();
  }

  next_page() {
    if (this.current_page < Math.ceil(this.total_records / this.page_size)) {
      this.current_page++;
      this.get_page(this.current_page);
    }
  }

  previous_page() {
    if (this.current_page > 1) {
      this.current_page--;
      this.get_page(this.current_page);
    }
  }

  get_page(page) {
    this.current_page = page;
    $("#showing_div").html("Loading...");

    // Prepare post data for pagination
    const postData = {
      Method: "get_item_page",
      Size: this.page_size,
      Page: this.current_page,
      textFilter: this.text_filter,
      Orb: this.display_order,
      ...this.list_filters
    };

    request(this.tableUri+ "/get_page", postData)
      .then(response => {
        if (response.status !== "error") {
          this.total_records = response.rows;
          this.records = response.items;
          this.last_record = response.latest;
          render_page(this.records);
        } else {
          $("#showing_div").html("No records found");
        }
      })
      .catch(error => {
        console.error('Error fetching page data:', error);
      });
  }
  get_pagination() {
    $("#showing_div").html("Loading...");
    this.page_size = Number($('input[name="p_size"]:checked').val());
    this.text_filter = $("#searchInput").val() || '-1';
    this.updateListFilters();
    // Prepare post data for pagination
    const postData = {
      Size: this.page_size,
      textFilter: this.text_filter,
      ...this.list_filters
    };
    console.log('dt: ' + JSON.stringify(postData))

    request(this.tableUri+ "/get_pagination", postData)
      .then(response => {
        if (response.status !== "error") {
          this.total_records = response.rows;
          this.records = response.items;
          this.last_record = response.latest;
          this.render_pagination(response.rows);
          this.get_page(1);
        } else {
          $("#showing_div").html("No records found");
        }
      })
      .catch(error => {
        console.error('Error fetching pagination data:', error);
      });
  }

  show_record(id) {
    // Implementation to show a record
  }

  edit_record(id) {
    // Implementation to edit an item
  }


  remove_record(id) {
    // Implementation to remove an item
  }

  restore_record(id) {
    // Implementation to restore an item
  }

  update_record(recordData) {
    // Implementation to update an item
  }

  create_record(recordData) {
    // Implementation to create an item
  }


  updateListFilters() {
    this.list_filters = {};
    $('.filters .select').each((index, element) => {
      const key = $(element).attr('name');
      const value = $(element).val();
      if (value !== "-1") {
        this.list_filters[key] = value;
      }
    });
  }
 
  render_pagination(total_rows) {
    let pageOptions = '';
    const pages_count = Math.ceil(total_rows / this.page_size);
    for (let i = 1; i <= pages_count; i++) {
      pageOptions += `<option value="${i}">${this.getPaginationText(total_rows, this.page_size, i)}</option>`;
    }
    $("#current_page").html(pageOptions);
    $("#t_records").html(`${total_rows} records`);
  }

  getPaginationText(totalItemsCount, numberOfItemsPerPage, page) {
    const start = (page - 1) * numberOfItemsPerPage + 1;
    const end = Math.min(start + numberOfItemsPerPage - 1, totalItemsCount);
    return `${start} to ${end} of ${totalItemsCount}`;
  }
}
// Example Base64 decode function
const Base64 = {
  decode: (str) => atob(str),
};

// Example date time format function
function date_time_format(date_str) {
  const date = new Date(date_str);
  return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
}