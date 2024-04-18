<?php

namespace App\Notify;

use App\Lib\CurlRequest;
use App\Models\UserNotification;
use App\Notify\Notifiable;
use App\Notify\NotifyProcess;

class PushNotification extends NotifyProcess implements Notifiable {

    public $deviceTokens;
    /**
     * Assign value to properties
     *
     * @return void
     */
    public function __construct() {

        $this->statusField    = 'push_notification_status';
        $this->body           = 'push_notification_body';
        $this->globalTemplate = 'push_notification_body';
        $this->notifyConfig   = 'push_config';

    }

    /**
     * Send notification
     *
     * @return void|bool
     */

    public function send() {

        $message    = $this->getMessage();
        $subject    = $this->subject;
        $remark     = $this->template->act;
        $clickValue = $this->clickValue;

        if ($this->setting->pn && $message) {
            try {
                if ($this->user) {
                    $data['priority'] = 'high';
                    if (count($this->deviceTokens) > 0) {
                        $data = [
                            "registration_ids" => $this->deviceTokens,
                            "notification"     => [
                                'title'        => $subject,
                                'body'         => $message,
                                'icon'         => getImage(getFilePath('logoIcon') . '/logo.png'),
                                'click_action' => $clickValue,
                            ],
                            'data'             => [
                                'remark' => $remark,
                            ],
                        ];

                        $dataString = json_encode($data);

                        $headers = [
                            'Authorization:key=' . $this->setting->push_config->serverKey,
                            'Content-Type: application/json',
                            'priority:high',
                        ];

                        $result = CurlRequest::curlPostContent('https://fcm.googleapis.com/fcm/send', $dataString, $headers);

                        if (@$result->results[0]->error) {
                            $this->createErrorLog('Push Notification Error: ' . $result->results[0]->error);
                            session()->flash('push_notification_error', $result->results[0]->error);
                        } else {
                            $userNotification              = new UserNotification();
                            $userNotification->title       = $subject;
                            $userNotification->user_id     = $this->user->id;
                            $userNotification->remark      = $remark;
                            $userNotification->click_value = $clickValue;
                            $userNotification->save();

                            $this->createLog('push_notification');
                        }

                        // Close connection
                        // curl_close($ch);
                    }
                }
            } catch (\Exception $e) {
                $this->createErrorLog('Push Notification Error: ' . $e->getMessage());
                session()->flash('push_notification_error', $e->getMessage());
            }
        }
    }

    /**
     * Configure some properties
     *
     * @return void
     */
    public function prevConfiguration() {
        //Check If User
        if ($this->user) {
            $this->deviceTokens = $this->user->deviceTokens()->pluck('token')->toArray();
            $this->receiverName = $this->user->fullname;
        }
        $this->toAddress = $this->deviceTokens;
    }
}
