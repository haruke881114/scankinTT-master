<section class="page-section" id="graphPage">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mt-0 graphTitle">圖表分析</h2>
                <hr class="divider my-4" />
                <p class="text-muted mb-5 graphSubtitle">點擊下拉式選單可查詢各年度數據幅度</p>
            </div>
        </div>

        <div id="chartGraph" class="col-lg-8 col-sm-11"></div>
        <div class="graph col-lg-3 col-sm-11">
            <h4 class="text-muted">選擇各個年度及月分</h4>
            <p class="text-muted" id="selectnum"></p>
            <div class="graphlBox">
                <select class="graphSelect" id='graphYear'>
                    <option value="2020">2020年</option>
                </select>
                <select class="graphSelect" id='graphMonth' onchange="getData()">
                    <option value="1">1月</option>
                    <option value="2">2月</option>
                    <option value="3">3月</option>
                    <option value="4">4月</option>
                    <option value="5">5月</option>
                    <option value="6">6月</option>
                    <option value="7">7月</option>
                    <option value="8">8月</option>
                    <option value="9">9月</option>
                    <option value="10">10月</option>
                    <option value="11">11月</option>
                    <option value="12">12月</option>
                </select>
            </div>
        </div>
        <script>
            var myChart = echarts.init(document.getElementById('chartGraph'));
            var disease = [],
                num = [],
                year = [];
            getData();

            function getData() {
                var year = $("#graphYear").val(), month = $("#graphMonth").val();
                document.getElementById("selectnum").innerHTML = "目前選擇： " + year + "年" + month + "月";

                //console.log("2222"); //測試用
                /* 設定處理 ajax 請求的 php 檔案 */
                /* 底下是利用發出 jQuery 的 ajax 請求方式去呼叫函式繪製 Google Chart 
                 * ajax 的各項屬性意義為.....
                 * url: 代表處理 ajax 請求的檔案
                 * type: 發出請求的方式。一般設定為 POST 或 GET
                 * dataType: 設定從伺服器回傳查詢結果的資料格式
                 * data: 要被送至伺服器處理的查詢字串。通常是傳送使用者輸入的查詢條件
                 * async: 是否採用非同步(async)的方式處理。預設值是 true
                 * success: 請求成功時所要執行的函式
                 * error: 請求失敗時所要執行的函式 
                 */
                var params = {
                    month: month
                };
                disease = [];
                num = [];
                year = [];
                console.log(params);
                $.ajax({
                    url: 'SQL/graphDB.php',
                    type: 'POST',
                    async: false,
                    data: params,
                    dataType: 'json',
                    /* 函式中的 backData 參數：伺服器回傳的查詢結果 */
                    success: function(backData, jqXHR) {
                        if (backData == "null") {
                            document.getElementById('main').innerHTML = "沒有資料!";
                        } else {
                            console.log(backData);
                            /* 將伺服器回傳的查詢結果傳給 drawChart()函式進行 chart 的繪製 */
                            for (var i = 0; i < backData.length; i++) {
                                disease.push(backData[i][0]);
                                num.push(backData[i][1]);
                                year.push(backData[i][3])
                            }
                        }
                    },
                    error: function(textStatus) {
                        console.log(" error!");
                    }
                })
                return disease, num, year;
            };

            $('#graphMonth').on('change', function() {
                myChart.setOption({
                    xAxis: {
                        type: 'category',
                        data: disease,
                        axisLabel: {
                            interval: 0,
                            margin: 12,
                            rotate: 45,
                            textStyle: {
                                color: "#222"
                            }
                        },
                    },

                    yAxis: {
                        type: 'value'
                    },
                    series: [{
                        data: num,
                        type: 'bar',
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true
                                }
                            }
                        },

                    }]
                });

            });

            var option = {
                color: ['#3398DB'],
                title: {
                    // text: '疾病統計',
                    // subtext: '點擊長條圖可查詢各年度數據幅度',
                    left: 'center',
                    show: true
                },
                tooltip: {
                    trigger: 'item',
                    left: 'center',
                    formatter: '{b} : {c} ',
                    showDelay: 0, // 显示延迟，添加显示延迟可以避免频繁切换，单位ms
                    axisPointer: { // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                xAxis: {
                    type: 'category',
                    data: disease,
                    axisLabel: {
                        interval: 0,
                        rotate: 45,
                        margin: 12,
                        textStyle: {
                            color: "#222"
                        }
                    },
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: num,
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            label: {
                                show: true
                            }
                        }
                    },

                }]
            };
            myChart.setOption(option);


            var x, i, j, l, ll, selElmnt, a, b, c;
            /*look for any elements with the class "custom-select":*/
            x = document.getElementsByClassName("custom-select");
            l = x.length;
            for (i = 0; i < l; i++) {
                selElmnt = x[i].getElementsByTagName("select")[0];
                ll = selElmnt.length;
                /*for each element, create a new DIV that will act as the selected item:*/
                a = document.createElement("DIV");
                a.setAttribute("class", "select-selected");
                a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
                x[i].appendChild(a);
                /*for each element, create a new DIV that will contain the option list:*/
                b = document.createElement("DIV");
                b.setAttribute("class", "select-items select-hide");
                for (j = 1; j < ll; j++) {
                    /*for each option in the original select element,
                    create a new DIV that will act as an option item:*/
                    c = document.createElement("DIV");
                    c.innerHTML = selElmnt.options[j].innerHTML;
                    c.addEventListener("click", function(e) {
                        /*when an item is clicked, update the original select box,
                        and the selected item:*/
                        var y, i, k, s, h, sl, yl;
                        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                        sl = s.length;
                        h = this.parentNode.previousSibling;
                        for (i = 0; i < sl; i++) {
                            if (s.options[i].innerHTML == this.innerHTML) {
                                s.selectedIndex = i;
                                h.innerHTML = this.innerHTML;
                                y = this.parentNode.getElementsByClassName("same-as-selected");
                                yl = y.length;
                                for (k = 0; k < yl; k++) {
                                    y[k].removeAttribute("class");
                                }
                                this.setAttribute("class", "same-as-selected");
                                break;
                            }
                        }
                        h.click();
                    });
                    b.appendChild(c);
                }
                x[i].appendChild(b);
                a.addEventListener("click", function(e) {
                    /*when the select box is clicked, close any other select boxes,
                    and open/close the current select box:*/
                    e.stopPropagation();
                    closeAllSelect(this);
                    this.nextSibling.classList.toggle("select-hide");
                    this.classList.toggle("select-arrow-active");
                });
            }

            function closeAllSelect(elmnt) {
                /*a function that will close all select boxes in the document,
                except the current select box:*/
                var x, y, i, xl, yl, arrNo = [];
                x = document.getElementsByClassName("select-items");
                y = document.getElementsByClassName("select-selected");
                xl = x.length;
                yl = y.length;
                for (i = 0; i < yl; i++) {
                    if (elmnt == y[i]) {
                        arrNo.push(i)
                    } else {
                        y[i].classList.remove("select-arrow-active");
                    }
                }
                for (i = 0; i < xl; i++) {
                    if (arrNo.indexOf(i)) {
                        x[i].classList.add("select-hide");
                    }
                }
            }
            /*if the user clicks anywhere outside the select box,
            then close all select boxes:*/
            document.addEventListener("click", closeAllSelect);
        </script>
    </div>
</section>