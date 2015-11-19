# yii2-zendesk
Yii2 plugin for zendesk service support (https://www.zendesk.com)

## How to use:
```php
use common\helpers\StringHelper;
use common\models\Feedback;
use hutsi\zendesk\Attachment;
use hutsi\zendesk\Search;
use hutsi\zendesk\Ticket;
use hutsi\zendesk\User;
use Yii;
use yii\web\UploadedFile;
```

If you wants to use uploads in your feedback form - you have to use ```\yii\web\UploadedFile``` instanse
```php
$uploadedFile = new UploadedFile(['tempName' => 'YOUR_FILE_TEMPNAME', 'name' => 'YOUR_FILE_NAME]);
```
Then - create and save zendesk Attachment instance from UploadedFile
```php
$zAttachment = new Attachment(['uploadedFile' => $uploadedFile]);
$token = $zAttachment->save();
```
You can also use zendesk search API for existing Users of your zendesk account
```php
$search = new Search(['query' => ['email' => '"derushev.alexey@gmail.com"']]);
if ($zUsers = $search->users()) {
    $zUser = $zUsers[0];
}
else {
    $zUser = new User(['email' => 'derushev.alexey@gmail.com');
    $zUser->save();
}
```
And finally, lets create a Ticket instance
```php
$zTicket = new Ticket([
    'requester_id' => $zUser->id,
    'requester' => [
        'email' => $zUser->email
    ],
    'subject' => StringHelper::truncate($feedback->message, 100),
    'comment' => [
        'body' => 'Authorization not works!'
    ],
]);
```
If we had an uploads - attach them to a ticket comment field
```php
$zTicket->comment['uploads'] = isset($token) && $token ? [$token] : null;
$zTicket->save();
```
Thats all.
