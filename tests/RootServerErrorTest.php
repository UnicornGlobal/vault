<?php


class RootServerErrorTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRootError()
    {
        $this->get('/');

        $this->assertEquals(
            '{"error":"Internal Server Error"}',
            $this->response->getContent()
        );
    }

    public function testBadMethod()
    {
        $this->delete('/config/app');

        $this->assertEquals(
            '{"error":"Internal Server Error"}',
            $this->response->getContent()
        );

        $this->assertEquals(
            '500',
            $this->response->status()
        );
    }

    public function testVersion()
    {
        $this->get('/api');

        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $user = factory('App\User')->make();

        $this->actingAs($user)->get('/api', [
            'App'           => env('APP_ID'),
            'Client'        => $user->app_id,
            'Authorization' => $user->app_key,
        ]);

        $this->assertEquals(
            'Lumen (5.7.8) (Laravel Components 5.7.*)',
            $this->response->getContent()
        );

        $this->assertEquals(
            '200',
            $this->response->status()
        );
    }
}
