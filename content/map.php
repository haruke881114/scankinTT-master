<section class="page-section" id="mapPage">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mt-0 mapTitle">地圖</h2>
                <hr class="divider my-4" />
                <p class="text-muted mapSubtitle">您可以搜尋離您最近的診所以利看診</p>
                <p class="text-muted mapSubtitle">使用滑鼠滾輪可以移動縮放地圖位置</p>
                <p class="text-muted mb-5 mapSubtitle">雙擊可查詢該區資料</p>
            </div>
        </div>

        <div id="chartMap" class="col-lg-8 col-sm-11 col-md-11"></div>
        <div class="hospital col-lg-4 col-sm-11 col-md-11">
            <h4 class="text-muted" id="district">請點選各區</h4>
            <div class="hospitalBox">
                <table id="hospitalList"> </table>
            </div>
        </div>
        <script>
            //            var myChart = echarts.init(document.getElementById('main'));
            var taiwan = "County/Taiwan.json";
            var app = {};

            // 定義 echarts 物件自訂的 extendsMap() 函式
            // 第一個參數（id）：繪製區域的元素 id
            // 第二個參數（opt）：繪製時的屬性設定
            echarts.extendsMap = function(id, opt) {
                // init() 函式：建立一個 echarts 實例
                // 它的參數是一個實例的容器。一般是一個 div 元素
                var chart = this.init(document.getElementById(id));

                // 記錄目前用來繪製的 geoJson
                var curGeoJson = {};

                // 記錄目前位於哪個縣市
                var city = [];

                // 設定用來繪製各區域的 geojson 檔
                var cityMap = {
                    "彰化縣": "County/Changhua.json",
                    "嘉義縣": "County/Chiayi.json",
                    "嘉義市": "County/ChiayiCity.json",
                    "新竹縣": "County/Hsinchu.json",
                    "新竹市": "County/HsinchuCity.json",
                    "花蓮縣": "County/Hualien.json",
                    "高雄市": "County/Kaohsiung.json",
                    "基隆市": "County/Keelung.json",
                    "金門縣": "County/Kinmen.json",
                    "連江縣": "County/Lienchiang.json",
                    "苗栗縣": "County/Miaoli.json",
                    "南投縣": "County/Nantou.json",
                    "新北市": "County/NewTaipei.json",
                    "澎湖縣": "County/Penghu.json",
                    "屏東縣": "County/Pingtung.json",
                    "臺中市": "County/Taichung.json",
                    "臺南市": "County/Tainan.json",
                    "臺北市": "County/Taipei.json",
                    "臺東縣": "County/Taitung.json",
                    "桃園市": "County/Taoyuan.json",
                    "宜蘭縣": "County/Yilan.json",
                    "雲林縣": "County/Yunlin.json"
                };

                // 定義要進行標記的座標點
                var geoCoordMap = {
                    "彰化縣": [120.32, 24.04],
                    "嘉義縣": [120.57, 23.46],
                    "嘉義市": [120.45, 23.48],
                    "新竹縣": [120.59, 24.46],
                    "新竹市": [120.58, 24.48],
                    "花蓮縣": [121.36, 23.59],
                    "高雄市": [120.17, 22.38],
                    "基隆市": [121.44, 25.08],
                    "金門縣": [118.25, 24.30],
                    "連江縣": [119.53, 26.12],
                    "苗栗縣": [120.49, 24.33],
                    "南投縣": [120.41, 23.54],
                    "新北市": [121.29, 25.00],
                    "澎湖縣": [119.33, 23.34],
                    "屏東縣": [120.29, 22.39],
                    "臺中市": [120.40, 24.09],
                    "臺南市": [120.12, 23.00],
                    "臺北市": [121.30, 25.03],
                    "臺東縣": [121.09, 22.49],
                    "桃園市": [121.18, 24.33],
                    "宜蘭縣": [121.29, 24.33],
                    "雲林縣": [120.32, 23.42]
                };

                // 定義標記點要使用的顏色
                var levelColorMap = {
                    '1': 'rgba(241, 109, 115, .8)',
                    '2': 'rgba(255, 235, 59, .7)',
                    '3': 'rgba(147, 235, 248, 1)'
                };

                // 設定繪製時的屬性選項值
                var defaultOpt = {
                    mapName: '台灣', // 要使用 & 繪製的地圖名稱
                    goDown: false, // 是否下鐵
                    bgColor: '#404a59', // 畫布背景顏色
                    activeArea: [], // 區域高亮,同 echarts 配置項
                    data: [],
                    // 下鐵後的回呼函式（點擊的地圖名、實例對象option、實例對象）
                    // 此目的能提供：當點擊某一區時，可以利用此回呼函式去顯示該區的地圖
                    callback: function(name, option, instance) {}
                };

                // 如果有傳入指定的屬性選項，則將它併入「預設選項」（defaultOpt）中
                if (opt) {
                    // extend() 函式：會將第二個參數的資料，合併至第一個參數
                    // 若有重覆的屬性，其值將會被覆蓋掉（請看下面的範例）
                    opt = this.util.extend(defaultOpt, opt);
                    // var aa = {'a1': '111', 'a2': '222'};
                    // var bb = {'b1': '333', 'a2': '444'};
                    // var kk = this.util.extend(aa, bb);
                    // console.log(kk);
                    // 上述顯示的結果為.....（在 aa 中 a2 的值將被覆蓋成 444）
                    // {'a1': '111', 'a2': '444', 'b1': '333'};
                };

                // 建立地圖被點擊時的層級索引
                // 初始狀態，陣列中僅有「台南」一個項目
                // 當點擊某一區，例如「安平區」，則陣列會有「台南」和「安平區」二項
                var areaName = [opt.mapName];

                // 用來記錄被點選的層級索引
                // 初始值是 0，代表初始狀態是「台南市」地圖
                var idx = 0;

                // 設定用來顯示「左上方導覽文字資訊」的位置
                var pos = {
                    // 用來設定新區域文字加入左上方導覽資訊的平移位置
                    leftPlus: 115,
                    leftCur: 150,
                    left: 198, // 用來設定初始畫面的左上方導覽資訊的位置
                    top: 50 // 用來設定初始畫面的左上方導覽資訊的位置
                };

                // 設定「左上方導覽文字資訊」，各層級之間的「〉」符號
                var lineAmongLevel = [
                    [0, 0],
                    [8, 11],
                    [0, 22]
                ];

                // 設定「左上方導覽文字資訊」的樣式
                var style = {
                    font: '18px "Microsoft YaHei", sans-serif',
                    textColor: '#eee',
                    lineColor: 'rgba(147, 235, 248, .8)'
                };

                var handleEvents = {
                    /**
                     * i：實例物件
                     * o：option（配置選項）
                     * n：地圖名稱
                     **/
                    resetOption: function(i, o, n) { // 此函式用來重新設定繪製選項的值
                        // 建立一個「左上方導覽文字資訊」的物件
                        var breadcrumb = this.createBreadcrumb(n);
                        // 尋找被點選的區域，是否在記錄「左上方導覽文字」的陣列中
                        // 如果找不到，indexOf() 將會回傳 -1
                        var j = areaName.indexOf(n);
                        var l = o.graphic.length;
                        if (j < 0) { // 此情況代表，被點選的區域目前不在導覽文字的陣列中
                            // 將新建立的「導覽文字資訊」物件加入
                            // 在初始狀態（i.e. 呈現第一層地圖時），graphic 陣列僅有二項資料
                            // (1) 第一項是導覽文字的上下線條，以及它的顯示位置 & 長度、、、
                            // (2) 第二項是第一層地圖的中英文名稱，以及它的文字大小 & 顯示位置、、、
                            // 若點選某一區域，呈現出第二層地圖時，graphic 陣列將會再加一項資料，亦即
                            // (3) 第三項是第二層地圖的中英文名稱，以及它的文字大小 & 顯示位置、、、
                            o.graphic.push(breadcrumb);
                            // 設定畫面左上方導覽文字資訊的上方線條長度
                            o.graphic[0].children[0].shape.x2 = 145;
                            // 設定畫面左上方導覽文字資訊的下方線條長度
                            o.graphic[0].children[1].shape.x2 = 145;
                            //                            console.log(l, o.graphic);
                            if (o.graphic.length > 2) { // 此條件成立，亦即點選了第二層地圖
                                // 利用 for 迴圈，逐一檢查那些標記點剛好落在被點選的區域中
                                // opt.data：記錄被標記點的相關資訊
                                for (var x = 0; x < opt.data.length; x++) {
                                    // 判斷被點選的區域（n）是否等於標記點的名稱（name）
                                    // （因為目前每一區域僅一標記點，且其 name 剛好是該區域的名稱）
                                    if (n === opt.data[x].name) {
                                        // 取得該標記點的資訊，並將其設定為 series 標記時的資料集（data）
                                        o.series[0].data = handleEvents.initSeriesData([opt.data[x]]);
                                        //                                        console.log(o);
                                        break;
                                    } else
                                        // 若找不到，則將 series 標記的資料集（data）設為空值
                                        o.series[0].data = [];
                                }
                            };
                            // 將被點選的區域加入地圖導覽資訊（areaName）的陣列中
                            areaName.push(n);
                            //                            console.log(areaName);
                            // 陣列的索引值加 1
                            idx++;
                        } else { // 此情況代表被點選的區域已記錄在導覽資訊（areaName）的陣列中
                            // splice() 是一個 javascript 函式
                            // 此處用來針對陣列的資料項目進行移除的動作
                            // 它的回傳值是被移除的項目
                            o.graphic.splice(j + 2, l);
                            if (o.graphic.length <= 2) { // 判斷是否為第一層地圖
                                // 設定畫面左上方導覽文字資訊的上方線條長度
                                // 相對於第二層地圖，此長度較短
                                o.graphic[0].children[0].shape.x2 = 60;
                                // 設定畫面左上方導覽文字資訊的下方線條長度
                                // 相對於第二層地圖，此長度較短
                                o.graphic[0].children[1].shape.x2 = 60;
                                // 取得該標記點的資訊，並將其設定為 series 標記時的資料集（data）
                                o.series[0].data = handleEvents.initSeriesData(opt.data);
                            };
                            // 移除地圖導覽資訊 areaName 中的第二層地圖
                            areaName.splice(j + 1, l);
                            // 設定目前被點選區域的陣列（地圖導覽資訊 areaName）索引值
                            idx = j;
                            // 重新設定導覽資訊文字列的上下線條長度
                            pos.leftCur -= pos.leftPlus * (l - j - 1);
                        };
                        o.geo.map = n; // 要重繪的地圖名稱，改為被點選的區域名稱
                        o.geo.zoom = 0.4; // 設定地圖的縮放比例  
                        i.clear(); // 清除現有的地圖物件（新地圖時再新建物件）
                        i.setOption(o); // 重新套用配置選項
                        this.zoomAnimation(); // 執行自訂的動畫函式
                        opt.callback(n, o, i); // 呼叫自行定義的回呼函式（目前函式內沒有作用）
                    },

                    /**
                     * name：地圖名稱
                     **/
                    createBreadcrumb: function(name) {
                        // 宣告中英對照的詞彙
                        var cityToPinyin = {
                            "彰化縣": "Changhua",
                            "嘉義縣": "Chiayi",
                            "嘉義市": "ChiayiCity",
                            "新竹縣": "Hsinchu",
                            "新竹市": "HsinchuCity",
                            "花蓮縣": "Hualien",
                            "高雄市": "Kaohsiung",
                            "基隆市": "Keelung",
                            "金門縣": "Kinmen",
                            "連江縣": "Lienchiang",
                            "苗栗縣": "Miaoli",
                            "南投縣": "Nantou",
                            "新北市": "NewTaipei",
                            "澎湖縣": "Penghu",
                            "屏東縣": "Pingtung",
                            "臺中市": "Taichung",
                            "臺南市": "Tainan",
                            "臺北市": "Taipei",
                            "臺東縣": "Taitung",
                            "桃園市": "Taoyuan",
                            "宜蘭縣": "Yilan",
                            "雲林縣": "Yunlin"
                        };

                        // 此物件用來設定與「左上方導覽文字」相關的資訊
                        var breadcrumb = {
                            type: 'group',
                            id: name,
                            left: pos.leftCur + pos.leftPlus,
                            top: pos.top + 3,
                            children: [ // 共包含三個物件：(1) 層級間的「〉」符號
                                // (2) 區域中文名稱；(3) 區域英文名稱
                                { // 此物件設定層級間「〉」符號的相關配置
                                    type: 'polyline',
                                    left: -90,
                                    top: -5,
                                    shape: { // 繪製出層級間的「〉」符號
                                        points: lineAmongLevel
                                    },
                                    style: {
                                        stroke: '#fff',
                                        // 利用 key 屬性記錄區域的中文名稱（在 click 事件中使用）
                                        key: name
                                    },
                                    onclick: function() { // 建立 click 事件
                                        // 取得被點擊的區域「中文名稱」
                                        var name = this.style.key;
                                        // 依取得的區域名稱，呼叫 resetOption() 函式重繪地圖
                                        handleEvents.resetOption(chart, option, name);
                                    }
                                }, { // 此物件設定「區域中文名稱」的相關配置
                                    type: 'text',
                                    left: -68,
                                    top: 'middle',
                                    style: {
                                        text: name, // 顯示被點擊區域的中文名稱
                                        textAlign: 'center',
                                        fill: style.textColor,
                                        font: style.font
                                    },
                                    onclick: function() { // 建立 click 事件
                                        // 取得被點擊的區域名稱
                                        var name = this.style.text;
                                        // 依取得的區域名稱，呼叫 resetOption() 函式重繪地圖
                                        handleEvents.resetOption(chart, option, name);
                                    }
                                }, { // 此物件設定「區域英文名稱」的相關配置
                                    type: 'text',
                                    left: -68,
                                    top: 10,
                                    style: {
                                        // 利用 name 屬性記錄區域的中文名稱（在 click 事件中使用）
                                        name: name,
                                        // 顯示被點擊區域的「英文名稱」
                                        // 若有找到該區域英文名稱，則將它們轉成大寫字母；否則，為空白
                                        text: cityToPinyin[name] ? cityToPinyin[name].toUpperCase() : '',
                                        textAlign: 'center',
                                        fill: style.textColor,
                                        font: '12px "Microsoft YaHei", sans-serif'
                                    },
                                    onclick: function() {
                                        // 取得被點擊的區域「中文名稱」
                                        var name = this.style.name;
                                        // 依取得的區域名稱，呼叫 resetOption() 函式重繪地圖
                                        handleEvents.resetOption(chart, option, name);
                                    }
                                }
                            ]
                        };
                        //                        console.log(breadcrumb);
                        pos.leftCur += pos.leftPlus;

                        return breadcrumb;
                    },

                    // 對於要進行標記的座標點，用來產生其相關資訊
                    initSeriesData: function(data) {
                        var temp = [];
                        // 對於每一筆傳入的資料（data），逐一讀取其資訊
                        // 同時，進一步組合成標記時會使用到的資訊物件
                        for (var i = 0; i < data.length; i++) {
                            // 依標記點的名稱（name）取得其經緯度座標位置
                            var geoCoord = geoCoordMap[data[i].name];
                            if (geoCoord) { // 若是該標記點有經緯度資料
                                // 利用 push() 函式，將物件加入 temp 陣列中
                                temp.push({ // 建立一個物件，包含二項屬性（name和value）
                                    // 此處 name 屬性的值，為標記點的名稱（data[i].name）
                                    name: data[i].name,
                                    // 此處 value 屬性值，包含經緯度、data[i].value 和 data[i].level）
                                    // 共有 4 項資料：經度、緯度、value、level
                                    value: geoCoord.concat(data[i].value, data[i].level)
                                });
                            }
                        };
                        // 回傳包含所有標記點（已組合好資訊）的陣列
                        return temp;
                    },

                    // 此函式主要用來產生地圖切換時的縮放及動畫
                    zoomAnimation: function() {
                        var count = null;
                        var zoom = function(per) {
                            if (!count)
                                count = per;
                            count = count + per;
                            // console.log(per,count);
                            chart.setOption({
                                geo: {
                                    zoom: count
                                }
                            });
                            if (count < 1)
                                // equestAnimationFrame() 函式解決了，瀏覽器不知道javascript動畫什麼時候開始、
                                // 以及不知道最佳迴圈間隔時間的問題。
                                // 它是跟著瀏覽器的繪製走的，如果瀏覽器繪製間隔是16.7ms，它就按這個間隔繪製；
                                // 如果瀏覽器繪製間隔是10ms, 它就按10ms繪製。
                                window.requestAnimationFrame(function() {
                                    zoom(0.2);
                                });
                        };
                        window.requestAnimationFrame(function() {
                            zoom(0.2);
                        });
                    }
                };

                // 設定要繪製地圖的配置選項
                var option = {
                    backgroundColor: opt.bgColor, // 繪製圖形的背景顏色
                    graphic: [ // 此 graphic 用來記錄繪製左上角導覽資訊所需的配置
                        // 例如，導覽區域的中英文名稱、上下兩條線、區域間的「〉」符號
                        { // 此物件用來建立左上方導覽文字的上下兩條線
                            type: 'group',
                            left: pos.left,
                            top: pos.top - 4,
                            children: [{ // 此物件設定左上方導覽文字「上方」的線條
                                type: 'line',
                                left: 0,
                                top: -20,
                                shape: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 60,
                                    y2: 0
                                },
                                style: {
                                    stroke: style.lineColor
                                }
                            }, { // 此物件設定左上方導覽文字「下方」的線條
                                type: 'line',
                                left: 0,
                                top: 20,
                                shape: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 60,
                                    y2: 0
                                },
                                style: {
                                    stroke: style.lineColor
                                }
                            }]
                        }, {
                            id: areaName[idx],
                            type: 'group',
                            left: pos.left + 2,
                            top: pos.top + 3,
                            children: [
                                //                                {
                                //                                    type: 'polyline',
                                //                                    left: 90,
                                //                                    top: -12,
                                //                                    shape: {
                                //                                        // 繪製出「左上方導覽文字資訊」各層級間的「〉」符號
                                //                                        points: lineAmongLevel
                                //                                    },
                                //                                    style: {
                                //                                        stroke: 'transparent',
                                //                                        key: areaName[idx]        // areaName[0] 記錄第一層地圖名稱
                                //                                    },
                                //                                    onclick: function () {
                                //                                        var name = this.style.key;
                                //                                        handleEvents.resetOption(chart, option, name);
                                //                                    }
                                //                                }, 
                                { // 此物件記錄左上方第一層導覽文字的「中文名稱」相關資訊
                                    type: 'text',
                                    left: 0,
                                    top: 'middle',
                                    style: {
                                        text: areaName[0],
                                        textAlign: 'center',
                                        fill: style.textColor,
                                        font: style.font
                                    },
                                    onclick: function() {
                                        // 點擊「台南市」文字，將會觸發 resetOption() 函式
                                        handleEvents.resetOption(chart, option, '台灣');
                                    }
                                }, { // 此物件記錄左上方第一層導覽文字的「英文名稱」相關資訊
                                    type: 'text',
                                    left: 0,
                                    top: 10,
                                    style: {
                                        text: 'Taiwan', // 第一層地圖的英文名稱
                                        textAlign: 'center',
                                        fill: style.textColor,
                                        font: '12px "Microsoft YaHei", sans-serif',
                                    },
                                    onclick: function() {
                                        // 點擊「Tainan」文字，將會觸發 resetOption() 函式
                                        handleEvents.resetOption(chart, option, '台灣');
                                    }
                                }
                            ]
                        }
                    ],
                    geo: { // 宣告一個地圖座標系組件
                        map: opt.mapName, // 要使用/繪製的地圖名稱
                        roam: true,
                        zoom: 1, // 當前視角的縮放比例
                        label: { // 圖形上的文字標籤
                            normal: { // 正常顯示時的樣式設定
                                show: true,
                                textStyle: {
                                    color: '#fff'
                                }
                            },
                            emphasis: { // 強調時（ex. 滑鼠移入）的樣式設定
                                textStyle: {
                                    color: '#fff'
                                }
                            }
                        },
                        itemStyle: { // 地圖區域的圖形樣式設定
                            normal: {
                                borderColor: 'rgb(181, 189, 201)',
                                borderWidth: 1,
                                areaColor: {
                                    type: 'radial',
                                    x: 0.5,
                                    y: 0.5,
                                    r: 0.8,
                                    colorStops: [{
                                        offset: 0,
                                        color: 'rgba(147, 235, 248, 0)' // 0% 處的顏色
                                    }, {
                                        offset: 1,
                                        color: 'rgba(147, 235, 248, .2)' // 100% 處的顏色
                                    }],
                                    globalCoord: false // 缺區為 false
                                },
                                shadowColor: 'rgba(128, 217, 248, 1)',
                                shadowOffsetX: -2,
                                shadowOffsetY: 2,
                                shadowBlur: 10
                            },
                            emphasis: {
                                areaColor: '#389BB7',
                                borderWidth: 0
                            }
                        },
                        // regions：在地圖中對特定的區域配置樣式
                        // 在此範例中，並沒有明顯的作用
                        regions: opt.activeArea.map(function(item) {
                            if (typeof item !== 'string') {
                                return {
                                    name: item.name,
                                    itemStyle: {
                                        normal: {
                                            areaColor: item.areaColor || '#389BB7'
                                        }
                                    },
                                    label: {
                                        normal: {
                                            show: item.showLabel,
                                            textStyle: {
                                                color: '#fff'
                                            }
                                        }
                                    }
                                };
                            } else {
                                return {
                                    name: item,
                                    itemStyle: {
                                        normal: {
                                            borderColor: '#91e6ff',
                                            areaColor: '#389BB7'
                                        }
                                    }
                                };
                            }
                        })
                    },
                    series: [{ // effectScatter：帶有漣漪特效動畫的散點（氣泡）圖
                        type: 'effectScatter',
                        coordinateSystem: 'geo',
                        //                             symbol: 'diamond',   // 此為菱形圖案
                        showEffectOn: 'render',
                        rippleEffect: {
                            period: 15,
                            scale: 6,
                            brushType: 'fill'
                        },
                        hoverAnimation: true,
                        itemStyle: {
                            normal: {
                                color: function(params) {
                                    // 以 value[3] 的值去選取要套用的顏色
                                    return levelColorMap[params.value[3]];
                                },
                                shadowBlur: 10,
                                shadowColor: '#333'
                            }
                        },
                        // 此部份設定要進行標記的座標點資料
                        data: handleEvents.initSeriesData(opt.data)
                    }]
                };

                // 設定圖表的配置項及數據
                chart.setOption(option);

                // 添加地圖點擊事件的處理函式
                chart.on('click', function(params) {
                    var _self = this;
                    // 假如被點擊的區域（or 地圖），與目前顯示的區域不同時
                    if (opt.goDown && params.name !== areaName[idx]) {
                        // 進一步判斷，被點擊的區域名稱，是否有相對應的 geojson 地圖檔
                        if (cityMap[params.name]) {
                            // 取得 geojson 地圖檔的檔名（可能含路徑資訊）
                            var url = cityMap[params.name];
                            // 利用 ajax 方式去讀取該 geojson 地圖檔的內容進行處理
                            $.get(url, function(response) {
                                // 記錄所讀取的 geojson 地圖檔資訊（此範例沒有進一步使用此變數）                          
                                curGeoJson = response;
                                // 對於被點擊的區域，註冊成目前可用的地圖
                                echarts.registerMap(params.name, response);
                                // 呼叫 resetOption() 函式，進行地圖版面的重新繪製
                                handleEvents.resetOption(_self, option, params.name);
                                //將變數更換為目前所在地區
                                city = params.name;
                            });
                        }
                    }
                });
                chart.on('dblclick', function(params) {
                    // var _self = this;
                    var district = params.name; ///雙擊抓取行政區名稱 
                    // console.log(district);
                    $("#district").html(city + " " + district + " 醫院資料");
                    $("#hospitalList").html("");
                    $("#hospitalList").append("<tr><th class='col-lg-2 col-sm-2' style='width:400px';>醫院名稱</th><th class='col-lg-3 col-sm-3'>地址</th><th class='col-lg-2 col-sm-2'>電話</th><th class='col-lg-6 col-sm-5' style='width:600px;'>營業時間</th></tr>");
                    var name = [],
                        add = [],
                        phone = [],
                        time = [];

                    $.ajax({
                        url: 'SQL/mapDB.php',
                        method: 'POST',
                        data: {
                            district: district
                        },
                        async: false,
                        dataType: 'json',
                        success: function(backData, jqXHR) {
                            if (backData == null) {
                                $("#hospitalList").html("此區暫時沒有資料！");
                            } else {
                                for (var i = 0; i < backData.length; i++) {
                                    //將營業時間做換行處理
                                    aa=backData[i][5].replace("星","A");
                                    aa=aa.replace(/星/g,"<br>星");
                                    aa=aa.replace("A","星");
                                    // console.log(aa);

                                    name.push(backData[i][0]);
                                    add.push(backData[i][3]);
                                    phone.push(backData[i][4]);
                                    time.push(aa);
                                    $("#hospitalList").append("<tr><td>" + name + "</td><td>" + add + "</td><td>"+ phone + "</td><td>" + time + "</td></tr>");

                                    name.pop();
                                    add.pop();
                                    phone.pop();
                                    time.pop();
                                }
                            }
                        },
                        error: function(textStatus) {
                            console.log(" error!");
                        }
                    })


                    // $("#district").html(city + " " + district + " 的醫院資料");
                    // $("#hospitalList").append("<li>" + name + "1" + "</li>");
                });

                // chart.setMap = function(mapName) {
                //     var _self = this;
                //     if (mapName.indexOf('區') < 0)
                //         mapName = mapName + '區';
                //     var citySource = cityMap[mapName];
                //     if (citySource) {
                //         // 取得 geojson 地圖檔的檔名（可能含路徑資訊）
                //         var url = citySource;
                //         // 利用 ajax 方式去讀取該 geojson 地圖檔的內容進行處理
                //         $.get(url, function(response) {
                //             // 記錄所讀取的 geojson 地圖檔資訊（此範例沒有進一步使用此變數）
                //             curGeoJson = response;
                //             // 對於被點擊的區域，註冊成目前可用的地圖
                //             echarts.registerMap(mapName, response);
                //             // 呼叫 resetOption() 函式，進行地圖版面的重新繪製
                //             handleEvents.resetOption(_self, option, mapName);

                //             console.log(mapName);
                //         });
                //     }
                //     handleEvents.resetOption(this, option, mapName);
                // };

                return chart;
            };

            // getJSON() 函式：載入 json 格式的資料
            // 第一個參數：要載入的 url 超連結，或 json 檔案名稱
            //              此處是載入 json 檔（檔名記錄在 tainan 變數中）
            // 第二個參數：它是回呼函式，當載入成功後，執行此函式
            //              其中的 getJson 是成功載入的資料集
            $.getJSON(taiwan, function(geoJson) {
                // registerMap() 函式：註冊可用的地圖
                // 第一個參數：註冊的地圖名稱（作為之後在 geo 或 map 組件中呼叫使用）
                //              在 geo 或 map 組件中的 map 屬性值即是對應至此值
                // 第二個參數：geoJson 格式的資料
                echarts.registerMap('台灣', geoJson);

                // 此處呼叫自行定義的 extendsMap() 函式。它有二個參數
                // 第一個參數：要繪製地圖的元素 id
                // 第二個參數：地圖的相關屬性選項設定（option）                
                echarts.extendsMap('chartMap', {
                    bgColor: '#06182d', // 畫布背景顏色
                    mapName: '台灣', // 要使用/繪製的地圖名稱
                    goDown: true, // 是否下鐵
                    // 下鐵後的回呼函式（點擊的地圖名、實例對象option、實例對象）
                    // 此目的能提供：當點擊某一區時，可以利用此回呼函式去顯示該區的地圖
                    callback: function(name, option, instance) {
                        // console.log(name, option, instance);
                    },
                    // 數據展示            	
                    // data: [{
                    //     name: '新北市',
                    //     value: 10,
                    //     level: 1
                    // }, {
                    //     name: '台北市',
                    //     value: 12,
                    //     level: 2
                    // }, {
                    //     name: '台南市',
                    //     value: 11,
                    //     level: 3
                    // }, {
                    //     name: '高雄市',
                    //     value: 16,
                    //     level: 2
                    // }, {
                    //     name: '台中市',
                    //     value: 12,
                    //     level: 1
                    // }]
                });
            });
        </script>
    </div>
</section>