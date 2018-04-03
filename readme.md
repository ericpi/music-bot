# MusicBot
如何使用 LINE BOT 做出自己的音樂機器人

使用前準備

1.需註冊一隻機器人 https://developers.line.me/ <br>
2.需要為此專案準備 https 網域 <br>
3.LINE BOT 需開啟 webhook 並取得 accessToken ，也要設定好指定網域 <br>
4.取得 KKBOX API 的 ID,SECRET,KEY https://docs-zhtw.kkbox.codes/docs <br>
5.因 IPHONE 只支援 m4a 格式，如需支援iphone需到 https://cloudconvert.com/ 註冊取得 KEY <br>
6.轉檔完成後的 m4a 檔案，需有地方可以存，並能提供網址，目前設定存在 S3，需公開儲存桶 <br>
  
  PS : 如不需要支援 iphone 可忽略 5 及 6 步驟
  
以上準備完成後就可以安裝專案

依照以下步驟輸入指令

```
git clone https://github.com/xup6m6fu04/music-bot.git

```

```
cd music-bot

```

```
composer install

```
```
php artisan key:generate

```
```
cp .env.example .env
```

建置專案完成後，請打開 .env 輸入以下內容

LINE_ACCESS_TOKEN=<br>

KKBOX_ID=<br>
KKBOX_SECRET=<br>
KKBOX_KEY=<br>

以下如不需支援 IPHONE 可以留空，如需支援都須填寫，並將 CONVERT_M4A 設定為 ON

AWS_KEY=<br>
AWS_SERECT=<br>
AWS_BUCKET=<br>
AWS_REGION=<br><br>
CloudConvert 網站取得的 key<br>
CONVERT_KEY=<br><br>
暫存音檔的本地位置(會刪除，不會佔用空間)<br>
LOCAL_MUSIC_PATH=<br><br>
CONVERT_M4A=<br>

這樣就完成囉！

詳細說明及實際作品展示與試用請參考我的部落格，有問題可在此留言或到部落格留言

https://blog.yorha2b.com/line-at-api-kkbox-api-azjle
