<!--Services-->
<section class="page-section bg-primary" id="servicesPage">
    <div class="container">
        <div class="row justify-content-center col-lg-12 col-sm-12">
            <div class="col-lg-6 col-sm-12" style="display:inline">
                <h2 class="title-h2">請上傳需辨識部位</h2>
                <img class="" id="uploadImg" style="" />
                <label class="btn btn-blue col-lg-6 col-sm-12" id="uploadBtn">
                    <input type="file" id="file" style="display:none;" class="" />
                    <i class="fas fa-upload"></i> &nbsp; Upload File
                    <!-- -->
                </label>
                <label class="btn btn-blue col-lg-6 col-sm-12" id="cameraBtn">
                    <input type="button" id="camera" capture="camera" style="display:none;" class="" />
                    <i class="fas fa-camera"></i> &nbsp; Camera
                    <!-- 相機問題尚未解決 -->
                </label><br>
                <label class="btn btn-danger col-lg-6 col-sm-12" id="nextBtn">
                    <input type="button" id="" style="display:none;" class="" />
                    <i class="fas fa-play"></i> &nbsp; NEXT
                </label><br>
            </div>
            <script>

            </script>
            <div class="col-lg-6 col-sm-12" style="display:inline;visibility:hidden" id="resultChoose">
                <h2 class="title-h2">可能病症為</h2>
                <ul>
                    <li class="title-h3">可點選以下症狀察看處理方式</li>
                </ul>
                <div class="resultBox" id="resultBox1">脂漏性皮膚炎 Seborrhea
                    <i class="resultBoxPercent" id="resultBox1Percent">94%</i>
                    <!-- 這邊是載入疾病百分比資料 -->
                </div>
                <div class="resultBox" id="resultBox2">紅斑性狼瘡 SLE
                    <i class="resultBoxPercent" id="resultBox1Percent">3%</i>
                </div>
                <div class="resultBox" id="resultBox3">牛皮癬 Psoriasis
                    <i class="resultBoxPercent" id="resultBox1Percent">2%</i>
                </div>
                <div class="resultBox" id="resultBox4">汗皰疹 Dyshidrosis
                    <i class="resultBoxPercent" id="resultBox1Percent">1%</i>
                </div>
            </div>



            <div class="col-lg-6" style="display:inline; display:none" id="resultDiv">
                <h2 class="title-h2">辨識結果</h2>
                <div class="resultBoxProcess"></div>
                <div class="resultBoxOther">
                    <h2 class="title-h2">外觀症狀</h2>
                    <div>
                        <p>
                            脂漏性皮膚炎是一種發炎反應，常發生在皮脂腺分佈較多的部位。
                            最常發作在頭皮（以頭皮屑表現）及臉部（鼻翼兩側、眉毛周圍、耳朵），另外也會在上胸、後背、臀部、外陰部這些區域出現。
                            伴隨較大塊的皮屑、皮屑發黃，及頭皮出現過度油膩、紅疹、搔癢。


                            <!-- 等資料庫建置完畢即可載入 -->
                        </p>
                    </div>
                    <h2 class="title-h2">建議處理</h2>
                    <div>
                        <p>
                            建議使用局部的類固醇以及抗黴菌藥膏來治療，
                            選用溫和的清潔劑、並保持適度清潔感染部位，
                            避免辛辣、刺激性食物，維持規律健康的生活習慣。

                            <!-- 等資料庫建置完畢即可載入 -->
                        </p>
                    </div>
                </div>
                <label class="btn btn-danger col-lg-6 col-sm-12" id="backBtn">
                    <input type="button" id="" style="display:none;" class="" />
                    <i class="fas fa-reply"></i> &nbsp; BACK
                </label><br>
            </div>
        </div>
        <script>
            $("#nextBtn").click(function() {
                $("#resultChoose").css("visibility", "inherit");

            });

            $(".resultBox").click(function() {
                $("#resultChoose").hide();
                detailed = $(this).text();
                console.log(detailed);
                detailedProcess = detailed.substr(0, 25);
                console.log(detailedProcess);
                $(".resultBoxProcess").text(detailedProcess);
                $("#resultDiv").show();
                $("#resultDiv").css("visibility", "inherit");

            });

            $("#backBtn").click(function() {
                $("#resultDiv").hide();
                $("#resultChoose").show();
                $("#resultChoose").css("visibility", "inherit");

            })
        </script>
</section>