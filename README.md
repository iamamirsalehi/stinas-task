# مستندات پروژه Stinas Task

## فهرست مطالب

1. [معرفی پروژه](#معرفی-پروژه)
2. [نصب و راه‌اندازی](#نصب-و-راه‌اندازی)
3. [معماری پروژه](#معماری-پروژه)
4. [الگوهای طراحی استفاده شده](#الگوهای-طراحی-استفاده-شده)
5. [ورود به سیستم](#ورود-به-سیستم)
6. [استفاده از پروژه](#استفاده-از-پروژه)

---

## معرفی پروژه

این پروژه یک سیستم مدیریت تیکت (Ticket Management System) است که با فریمورک Laravel 12 و PHP 8.3 توسعه یافته است. این سیستم امکان ثبت تیکت توسط کاربران و بررسی و تایید/رد تیکت‌ها توسط ادمین‌ها را فراهم می‌کند.

### ویژگی‌های اصلی:
- سیستم احراز هویت جداگانه برای کاربران و ادمین‌ها
- ثبت تیکت با امکان پیوست فایل
- فرآیند تایید چندمرحله‌ای تیکت‌ها
- ارسال تیکت‌های تایید شده به سرویس خارجی
- سیستم صف (Queue) برای پردازش غیرهمزمان
- لاگ‌گیری از فراخوانی‌های سرویس خارجی

---

## نصب و راه‌اندازی

### پیش‌نیازها

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git

### مراحل نصب

#### 1. کلون کردن پروژه

```bash
git clone <repository-url>
cd stinas-task
```

#### 2. راه‌اندازی با Docker

**روش اول: استفاده از اسکریپت خودکار**

```bash
chmod +x docker/setup.sh
./docker/setup.sh
```

#### 3. دسترسی به سرویس‌ها

پس از راه‌اندازی، سرویس‌های زیر در دسترس خواهند بود:

- **اپلیکیشن**: http://localhost:4000
- **PHPMyAdmin**: http://localhost:4002
- **MySQL**: localhost:4001

#### 4. اطلاعات دسترسی PHPMyAdmin

برای دسترسی به PHPMyAdmin:

1. به آدرس http://localhost:4002 بروید
2. در پنجره HTTP Basic Authentication:
   - **نام کاربری**: `admin`
   - **رمز عبور**: `admin123`
3. پس از ورود، در صفحه PHPMyAdmin از اطلاعات زیر استفاده کنید:
   - **Server**: `mysql` (یا خالی بگذارید)
   - **Username**: `stinas_user` (یا `root` برای دسترسی کامل)
   - **Password**: رمز عبور MySQL از فایل `.env` (`DB_PASSWORD` یا `DB_ROOT_PASSWORD`)

**نکته**: می‌توانید اطلاعات HTTP Basic Authentication را در فایل `.env` با تغییر `PMA_USER` و `PMA_PASSWORD` تغییر دهید.

### دستورات مفید Docker

```bash
# مشاهده لاگ‌ها
docker-compose logs -f app
docker-compose logs -f mysql

# اجرای دستورات Artisan
docker-compose exec app php artisan [command]

# دسترسی به شل کانتینر
docker-compose exec app sh

# توقف سرویس‌ها
docker-compose down

# توقف و حذف حجم‌ها (هشدار: داده‌های دیتابیس حذف می‌شود)
docker-compose down -v

# ساخت مجدد کانتینرها
docker-compose build --no-cache

# اجرای تست‌ها
docker-compose exec app php artisan test
```

---

## معماری پروژه

### ساختار کلی

پروژه از معماری لایه‌ای (Layered Architecture) پیروی می‌کند که شامل لایه‌های زیر است:

```
app/
├── Http/              # لایه Presentation (کنترلرها و Request ها)
├── Services/          # لایه Business Logic (منطق کسب‌وکار)
├── Infrastructure/    # لایه Infrastructure (پیاده‌سازی‌های فنی)
├── Models/            # لایه Domain (مدل‌های دامنه)
├── Events/            # رویدادهای سیستم
├── Listeners/         # شنونده‌های رویدادها
└── Jobs/              # کارهای صف
```

### چرا پروژه ماژولار نشده است؟

پروژه به صورت ماژولار (Modular) طراحی نشده است و این تصمیم عمدی بوده است. دلایل این تصمیم:

1. **نیاز به بحث با متخصص دامنه (Domain Expert)**: برای تعیین مرزهای دقیق ماژول‌ها (Bounded Contexts) نیاز به مشورت با متخصص دامنه وجود دارد. بدون درک کامل از مرزهای کسب‌وکار، ماژولار کردن می‌تواند منجر به طراحی نادرست شود.

2. **عدم قطعیت در مرزها**: تا زمانی که مرزهای دقیق دامنه مشخص نشده‌اند، بهتر است از ساختار ساده‌تر استفاده شود و در صورت نیاز، در آینده به معماری ماژولار مهاجرت کرد.

### جداسازی لایه‌ها

پروژه با استفاده از الگوهای طراحی مناسب، جداسازی واضحی بین لایه‌ها ایجاد کرده است:

- **لایه Infrastructure**: تمام پیاده‌سازی‌های فنی (دیتابیس، سرویس‌های خارجی) در این لایه قرار دارند
- **لایه Business Logic**: منطق کسب‌وکار در کلاس‌های Service قرار دارد
- **لایه Presentation**: کنترلرها فقط مسئول دریافت درخواست و ارسال پاسخ هستند

---

## الگوهای طراحی استفاده شده

### 1. الگوی Repository

الگوی Repository برای جداسازی منطق دسترسی به داده از منطق کسب‌وکار استفاده شده است.

#### ساختار:

```
app/Infrastructure/Persist/Repository/
├── UserRepository.php              # Interface
├── AdminRepository.php              # Interface
├── TicketRepository.php             # Interface
└── Eloquent/
    ├── EloquentUserRepository.php   # پیاده‌سازی با Eloquent
    ├── EloquentAdminRepository.php
    └── EloquentTicketRepository.php
```

#### مزایا:

- **جداسازی Infrastructure از Business Logic**: منطق کسب‌وکار به نحوه ذخیره‌سازی داده‌ها وابسته نیست
- **قابلیت تست**: می‌توان Repository های Mock برای تست ایجاد کرد
- **قابلیت تغییر**: می‌توان بدون تغییر در لایه Business، ORM یا دیتابیس را تغییر داد

#### مثال استفاده:

```php
// در Service
class TicketService
{
    public function __construct(
        private TicketRepository $ticketRepository
    ) {}
    
    public function add(AddNewTicket $addNewTicket): void
    {
        // استفاده از Repository بدون وابستگی به Eloquent
        $ticket = new Ticket(...);
        $this->ticketRepository->save($ticket);
    }
}
```

#### ثبت در Service Provider:

Repository ها در `RepositoryServiceProvider` ثبت می‌شوند:

```php
$this->app->bind(TicketRepository::class, function ($app) {
    return new EloquentTicketRepository(new Ticket());
});
```

### 2. الگوی Adapter

الگوی Adapter برای ارتباط با سرویس‌های خارجی استفاده شده است.

#### ساختار:

```
app/Services/ExternalService/
├── ExternalServiceAdapter.php      # Interface
└── FakeExternalServiceAdapter.php  # پیاده‌سازی تست
```

#### مزایا:

- **جداسازی**: منطق کسب‌وکار به پیاده‌سازی خاص سرویس خارجی وابسته نیست
- **قابلیت تست**: می‌توان Adapter های Mock برای تست ایجاد کرد
- **انعطاف‌پذیری**: می‌توان بدون تغییر در کد اصلی، سرویس خارجی را تغییر داد

#### مثال استفاده:

```php
interface ExternalServiceAdapter
{
    public function sendTicket(Ticket $ticket): bool;
}

// در Listener
class TicketFinalApprovedListener
{
    public function __construct(
        private ExternalServiceAdapter $externalServiceAdapter
    ) {}
    
    public function handle(TicketFinalApprovedEvent $event): void
    {
        $this->externalServiceAdapter->sendTicket($event->ticket);
    }
}
```

#### ثبت در Service Provider:

```php
$this->app->bind(
    ExternalServiceAdapter::class, 
    FakeExternalServiceAdapter::class
);
```

### 3. Single Action Controller

تمام کنترلرهای پروژه از الگوی Single Action Controller استفاده می‌کنند.

#### ساختار:

هر کنترلر فقط یک متد `__invoke` دارد که یک عملیات خاص را انجام می‌دهد.

```php
class ApproveController extends Controller
{
    public function __construct(
        private TicketApproveService $ticketApproveService
    ) {}
    
    public function __invoke(ApproveRequest $request, int $id): RedirectResponse
    {
        // منطق کنترلر
    }
}
```

#### مزایا:

- **سادگی**: هر کنترلر فقط یک مسئولیت دارد
- **خوانایی**: نام کلاس به وضوح نشان می‌دهد که چه کاری انجام می‌دهد
- **قابلیت تست**: تست کردن کنترلرهای تک عملیاتی ساده‌تر است
- **سازماندهی بهتر**: فایل‌های کوچک‌تر و متمرکزتر

#### استفاده در Routes:

```php
Route::post('/admin/tickets/{id}/approve', ApproveController::class)
    ->name('admin.tickets.approve');
```

---

## ورود به سیستم

### ورود به عنوان کاربر (User)

1. به آدرس http://localhost:4000 بروید
2. روی لینک "Login" کلیک کنید یا به آدرس `/login` بروید
3. نام کاربری و رمز عبور خود را وارد کنید
4. پس از ورود موفق، به داشبورد کاربر هدایت می‌شوید

**نکته**: برای ایجاد حساب کاربری جدید، می‌توانید از صفحه ثبت‌نام (`/register`) استفاده کنید.

### ورود به عنوان ادمین (Admin)

1. به آدرس http://localhost:4000/admin/login بروید
2. نام کاربری و رمز عبور ادمین را وارد کنید

#### حساب‌های ادمین پیش‌فرض:

پس از اجرای سیدر (`php artisan db:seed`)، دو حساب ادمین پیش‌فرض ایجاد می‌شود:

- **نام کاربری**: `admin1`
- **رمز عبور**: `password`

- **نام کاربری**: `admin2`
- **رمز عبور**: `password`

**هشدار امنیتی**: در محیط Production حتماً رمزهای عبور پیش‌فرض را تغییر دهید!

### تفاوت‌های احراز هویت

پروژه از دو Guard جداگانه برای کاربران و ادمین‌ها استفاده می‌کند:

- **Guard کاربران**: `web` (پیش‌فرض)
- **Guard ادمین**: `admin`

این جداسازی در فایل `config/auth.php` تعریف شده است:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],
```

---

## استفاده از پروژه

### برای کاربران

#### ثبت تیکت جدید

1. وارد داشبورد کاربر شوید
2. روی "Create New Ticket" کلیک کنید
3. عنوان، توضیحات و فایل پیوست (اختیاری) را وارد کنید
4. تیکت را ثبت کنید

#### مشاهده تیکت‌های خود

در داشبورد کاربر، لیست تمام تیکت‌های ثبت شده توسط شما نمایش داده می‌شود.

### برای ادمین‌ها

#### مشاهده لیست تیکت‌ها

پس از ورود به عنوان ادمین، در داشبورد ادمین لیست تمام تیکت‌ها نمایش داده می‌شود.

#### بررسی تیکت

1. روی یک تیکت کلیک کنید تا جزئیات آن را مشاهده کنید
2. می‌توانید فایل پیوست را دانلود کنید
3. می‌توانید تیکت را تایید یا رد کنید

#### تایید تیکت

تیکت‌ها در چند مرحله تایید می‌شوند:
- هر ادمین می‌تواند تیکت را در مرحله خود تایید کند
- پس از تایید نهایی، تیکت به سرویس خارجی ارسال می‌شود

#### رد تیکت

- ادمین می‌تواند تیکت را رد کند و دلیل رد را ثبت کند
- پس از رد، ایمیل به کاربر ارسال می‌شود

#### عملیات دسته‌ای (Bulk Operations)

- می‌توانید چند تیکت را به صورت همزمان تایید یا رد کنید

---

## ساختار دیتابیس

### جداول اصلی

- **users**: اطلاعات کاربران
- **admins**: اطلاعات ادمین‌ها
- **tickets**: تیکت‌ها
- **ticket_approve_steps**: مراحل تایید تیکت
- **ticket_notes**: یادداشت‌های تیکت
- **external_service_call_logs**: لاگ فراخوانی‌های سرویس خارجی

---

## تست‌ها

پروژه از Pest برای تست استفاده می‌کند.

### اجرای تست‌ها

```bash
docker-compose exec app php artisan test
```

یا:

```bash
docker-compose exec app vendor/bin/pest
```

---

## لاگ‌ها

لاگ‌های Laravel در مسیر `storage/logs/laravel.log` ذخیره می‌شوند.

برای مشاهده لاگ‌ها در Docker:

```bash
docker-compose exec app tail -f storage/logs/laravel.log
```

## پشتیبانی

برای سوالات و مشکلات، لطفاً Issue ایجاد کنید یا با تیم توسعه تماس بگیرید.

---

**نسخه مستندات**: 1.0  
**آخرین به‌روزرسانی**: 2025
