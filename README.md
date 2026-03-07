# 拼豆在线订购系统（Laravel）

课程项目实现：`CS5281 Internet Application Development`  
技术栈：`Laravel + Blade + JavaScript + Session + JSON 文件存储`

## 功能覆盖

- 首页商品展示与名称搜索
- 首页加入购物车（AJAX 无刷新，不跳转购物车）
- 购物车（数量实时计算 + AJAX 无刷新同步 + 正整数校验）
- 用户登录/退出（写死演示账号）
- 角色分离（先登录再进入对应用户/管理员页面）
- 下单与结算（前后端双重校验，登录用户下单并扣减库存）
- 用户历史订单（查看订单状态，不支持用户自行取消）
- 管理员后台商品 CRUD
- 管理员订单管理（发货 / 取消）
- 管理员统计报表（待处理订单、营业额、热销商品）

## 运行方式

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

打开：`http://127.0.0.1:8000`

## 管理员账号

- 用户名：`admin`
- 密码：`admin123`

## 用户演示账号（写死）

- `alice / alice123`
- `bob / bob123`

可在 `.env` 中修改：

- `ADMIN_USERNAME`
- `ADMIN_PASSWORD`

## 数据存储（无数据库依赖）

- 商品：`storage/app/data/products.json`
- 订单：`storage/app/data/orders.json`
- 管理员上传商品图：`public/images/uploads/products/`（JSON 仅保存图片路径）

可选配置（默认无需改）：

- `SHOP_DATA_DIR`：自定义 JSON 存储目录

