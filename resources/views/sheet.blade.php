<!doctype html>
<html>

<head>
    <meta charset='utf-8'>
    <title>Excel Example</title>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <link rel="stylesheet" href="https://unpkg.com/x-data-spreadsheet@1.1.5/dist/xspreadsheet.css">
    <script src="https://unpkg.com/x-data-spreadsheet@1.1.5/dist/xspreadsheet.js"></script>
    <script src="https://unpkg.com/x-data-spreadsheet@1.1.5/dist/locale/en.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.2/xlsx.full.min.js"></script>
    <style>
        /* Demo Styles */
        #content {
            margin: 0 auto;
            width: 100%;
        }

        h1 {
            font-size: 40px;
        }

        /* The Loader */
        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            overflow: hidden;
        }

        .no-js #loader-wrapper {
            display: none;
        }

        #loader {
            display: block;
            position: relative;
            left: 50%;
            top: 50%;
            width: 150px;
            height: 150px;
            margin: -75px 0 0 -75px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #16a085;
            -webkit-animation: spin 1.7s linear infinite;
            animation: spin 1.7s linear infinite;
            z-index: 11;
        }

        #loader:before {
            content: "";
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #e74c3c;
            -webkit-animation: spin-reverse 0.6s linear infinite;
            animation: spin-reverse 0.6s linear infinite;
        }

        #loader:after {
            content: "";
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #f9c922;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin-reverse {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(-360deg);
            }
        }

        @keyframes spin-reverse {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-360deg);
            }
        }

        #loader-wrapper .loader-section {
            position: fixed;
            top: 0;
            width: 51%;
            height: 100%;
            background: #222;
            z-index: 10;
        }

        #loader-wrapper .loader-section.section-left {
            left: 0;
        }

        #loader-wrapper .loader-section.section-right {
            right: 0;
        }

        /* Loaded styles */
        .loaded #loader-wrapper .loader-section.section-left {
            transform: translateX(-100%);
            transition: all 0.7s 0.3s cubic-bezier(0.645, 0.045, 0.355, 1);
        }

        .loaded #loader-wrapper .loader-section.section-right {
            transform: translateX(100%);
            transition: all 0.7s 0.3s cubic-bezier(0.645, 0.045, 0.355, 1);
        }

        .loaded #loader {
            opacity: 0;
            transition: all 0.3s ease-out;
        }

        .loaded #loader-wrapper {
            visibility: hidden;
            transform: translateY(-100%);
            transition: all 0.3s 1s ease-out;
        }

    </style>
</head>

<body>

    <div id="loader-wrapper">
        <div id="loader"></div>

        <div class="loader-section section-left"></div>
        <div class="loader-section section-right"></div>

    </div>

    <div id="content">
        <div id="spreadsheet"></div>
    </div>
    <script>
        // var tRows = 0;
        // var totCols = 0;
        // var eData = [];
        $(function() {
            $.ajax({
                type: "GET",
                url: "{{ route('xlsx').'?f='.$f }}",
                // data: "data",
                dataType: "json",
                success: function(response) {
                    eData = response;
                    console.log(response[0].length);
                    esheet(response, response.length, response[0].length);
                }
            });
        });

        function esheet(ed, tRows, totCols) {
            const massage = wb => {
                const out = [];
                // wb.SheetNames.forEach(name => {
                const o = {
                    name: "sheet1",
                    rows: {}
                };
                // const ws = wb.Sheets[name];
                // const aoa = XLSX.utils.sheet_to_json(ws, {
                //     raw: false,
                //     header: 1
                // });
                console.log(wb);
                wb.forEach((r, i) => {
                    const cells = {};
                    r.forEach((c, j) => {
                        cells[j] = {
                            text: c
                        };
                    });
                    o.rows[i] = {
                        cells
                    };
                });
                out.push(o);
                // });
                return out;
            };


            const s = x_spreadsheet('#spreadsheet', {
                mode: 'edit', // edit | read
                showToolbar: false,
                showGrid: true,
                showContextmenu: true,
                view: {
                    height: () => document.documentElement.clientHeight,
                    width: () => document.documentElement.clientWidth,
                },
                row: {
                    len: tRows + 30,
                    height: 25,
                },
                col: {
                    len: totCols + 5,
                    width: 150,
                    indexWidth: 60,
                    minWidth: 60,
                },
                style: {
                    bgcolor: '#ffffff',
                    align: 'left',
                    valign: 'middle',
                    textwrap: false,
                    strike: false,
                    underline: false,
                    color: '#0a0a0a',
                    font: {
                        name: 'Helvetica',
                        size: 10,
                        bold: false,
                        italic: false,
                    },
                },
            });

            window.s = s;
            var obj1 = massage(ed);
            s.loadData(obj1);
            $('body').addClass('loaded');

            s.change((data) => {
                // console.log(data);
            });
            s.on('cell-edited', (text, ri, ci) => {
                $.ajax({
                    type: "GET",
                    url: "{{route('xlsxupd')}}",
                    data: { ri:ri, ci:ci, v:text, f:'{{$f}}' },
                    dataType: "json",
                    success: function (response) {
                        //
                    }
                });
                // console.log(text, ri, ci);
            });
            // s.validate();

            //   .loadData(JSON.parse('{!! json_encode($data) !!}'))
            //   .change(data => {
            //       alert('ok');
            //   });



            function xtos(sdata) {
                var out = XLSX.utils.book_new();
                sdata.forEach(function(xws) {
                    var aoa = [
                        []
                    ];
                    var rowobj = xws.rows;
                    for (var ri = 0; ri < rowobj.len; ++ri) {
                        var row = rowobj[ri];
                        if (!row) continue;
                        aoa[ri] = [];
                        /* eslint-disable no-loop-func */
                        Object.keys(row.cells).forEach(function(k) {
                            var idx = +k;
                            if (isNaN(idx)) return;
                            aoa[ri][idx] = row.cells[k].text;
                        });
                    }
                    var ws = XLSX.utils.aoa_to_sheet(aoa);
                    XLSX.utils.book_append_sheet(out, ws, xws.name);
                });
                return out;
            }

            window.export_xlsx = function() {
                /* build workbook from the grid data */
                var new_wb = xtos(s.getData());
                console.log(new_wb);

                /* generate download */
                // this is what you would normally use
                //XLSX.writeFile(new_wb, "SheetJS.xlsx");
                // codesandbox messes with the logic, so we need to do it manually
                var ab = XLSX.write(new_wb, {
                    bookType: "xlsx",
                    type: "array"
                });
                var blob = new Blob([ab]);
                var url = URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.download = "SheetJS.xlsx";
                a.href = url;
                document.body.appendChild(a);
                a.click();
            };
        }
    </script>
</body>

</html>
