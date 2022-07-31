<?php

class ModelExtensionModuleInstagram extends Model
{
    private $CURL;

    const POST_INFO = 'me/media';
    const TOKEN_REFRESH = 'refresh_access_token';
    const SEPARATOR = '/';

    public function __construct($params)
    {
        parent::__construct($params);
        $this->log = new Log('instagram.log');
        $this->cache = new Cache('file', 3600 * 1);
    }

    public function request($data)
    {
        $ch = $this->getCURL();
        $accessToken = $this->config->get('instagram_token');
        //$accessToken = 'IGQVJYWk5tWHhzWmszSmhmbHJlbXMxZAHdlUkw5RlpVN3FOZAVUtc0NMNEl1Nm1VaWlvS2x0eGF1ekRra1I3ZAXRYV1FXa2pKQWcwaGlyUS1WVDdNNVNpdm9BUmVaYzNmLU9SeWlDZAVl3';
        $query = http_build_query(array_merge($data['fields'], array('access_token' => $accessToken)));
        $url = implode(self::SEPARATOR, [
            INSTAGRAM_BASE,
            self::POST_INFO,
        ]);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($data['method'] == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        } elseif (!empty($query)) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);
        }

        try {
            $res_str = curl_exec($ch);
            $header = substr($res_str, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($res_str, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $ah = explode("\n", $header);
            if (strpos($ah[0], '200') == FALSE)
                throw new Exception("$res_str\n" . curl_getinfo($ch, CURLINFO_HTTP_CODE));
            $res = json_decode($body);
            if ($res === NULL)
                throw new Exception('JSON is empty:\n' . $body);
            return $res;
        } catch (Exception $e) {
            $error = new stdClass();
            $error->success = false;
            $error->error = 'Ошибка коммуникации с "Instagram": ' . $e->getMessage();
            $this->log->write(sprintf("error request instagram: answer (%s); query: (%s); url (%s)", $e->getMessage(), $query, $url));
            $ret = new stdClass();
            $ret->errors = array($error);
            return $ret;
        }
    }

    public function getFeed()
    {
        $Cache = new Cache('file', 3600 * 24);
        $ret = $Cache->get('instagram_feed');

        if ($ret)
            return $ret;
        $res = $this->request(array(
            'method' => 'GET',
            'fields' => [
                'fields' => 'id,caption,media_url,permalink'
            ],
        ));

        if (isset($res->data) and $res->data) {
            $ret = [];

            foreach ($res->data as $post) {
                $ret[] = array(
                    'link' => $post->permalink,
                    'caption' => $post->caption,
                    'image' => $post->media_url,
                );
            }

            $Cache->set('instagram_feed', $ret);
            return $Cache->get('instagram_feed');
        } else {
            return [];
        }
    }

    protected function getCURL()
    {
        if ($this->CURL === NULL) {
            $this->CURL = curl_init();
            curl_setopt($this->CURL, CURLOPT_RETURNTRANSFER, TRUE);
        }
        return $this->CURL;
    }

    /**
     * Обновляет текущий (долгосрочный токен). Если его не обновлять, то через 60 дней он отвалится и придётся проходить всю
     * процедуру получения заново.
     * Мануал тут https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens
     * Пример обновления по SSH: curl -X GET 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&&access_token=IGQV'
     * Пример обновления по HTTP: https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&&access_token=IGQV
     */
    public function updateToken ()
    {
        $ch = $this->getCURL();
        $currentToken = $this->config->get('instagram_token');
        $query = http_build_query([
            'grant_type' => 'ig_refresh_token',
            'access_token' => $currentToken
        ]);

        $url = implode(self::SEPARATOR, [
            INSTAGRAM_BASE,
            self::TOKEN_REFRESH,
        ]);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $query);

        try {
            $res_str = curl_exec($ch);
            $header = substr($res_str, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($res_str, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $ah = explode("\n", $header);
            if (strpos($ah[0], '200') == FALSE)
                throw new Exception("$res_str\n" . curl_getinfo($ch, CURLINFO_HTTP_CODE));
            $res = json_decode($body);
            if ($res === NULL)
                throw new Exception('JSON is empty:\n' . $body);

            $token = $res->access_token;

            if (!empty($token)) {
                $this->model_setting_setting->editSettingValue('instagram', 'instagram_token', $token);
                return $res;
            } else {
                $this->log->write(sprintf("error update instagram token: answer (%s); query: (%s); url (%s)", $body, $query, $url));
                return false;
            }
        } catch (Exception $e) {
            $error = new stdClass();
            $error->success = false;
            $error->error = 'Ошибка коммуникации с "Instagram": ' . $e->getMessage();
            $this->log->write(sprintf("error update instagram token: answer (%s); query: (%s); url (%s)", $e->getMessage(), $query, $url));
            $ret = new stdClass();
            $ret->errors = array($error);
            return $ret;
        }
    }
}