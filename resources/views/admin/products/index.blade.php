@extends('layouts.app')

@section('title', '后台商品管理')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
        <h2>后台商品管理</h2>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-muted">退出管理员</button>
        </form>
    </div>

    <div class="card mb-2">
        <h3 style="margin-top: 0;">新增商品</h3>
        <form action="{{ route('admin.products.store') }}" method="POST" class="row" enctype="multipart/form-data">
            @csrf
            <div class="col-4">
                <label>名称</label>
                <input class="input mt-1" name="name" required>
            </div>
            <div class="col-2">
                <label>价格</label>
                <input class="input mt-1" name="price" type="number" step="0.01" min="0.01" required>
            </div>
            <div class="col-2">
                <label>库存</label>
                <input class="input mt-1" name="stock" type="number" min="0" value="0" required>
            </div>
            <div class="col-2">
                <label>状态</label>
                <select class="select mt-1" name="status" required>
                    <option value="active">active</option>
                    <option value="inactive">inactive</option>
                </select>
            </div>
            <div class="col-6">
                <label>图片路径（可选）</label>
                <input class="input mt-1" name="image" placeholder="/images/products/default.svg">
            </div>
            <div class="col-6">
                <label>本地上传（可选）</label>
                <input class="input mt-1" name="image_file" type="file" accept="image/*">
                <div class="text-muted mt-1" style="font-size: 12px;">上传后会保存到 <code>public/images/uploads/products</code>，并把路径写入 JSON。</div>
            </div>
            <div class="col-12">
                <label>描述</label>
                <input class="input mt-1" name="description" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">创建商品</button>
            </div>
        </form>
    </div>

    @if (count($products) === 0)
        <div class="card">
            <p>暂无商品。</p>
        </div>
    @else
        @foreach ($products as $product)
            <div class="card mb-2">
                <div class="row">
                    <div class="col-4">
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="product-image">
                    </div>
                    <div class="col-8">
                        <form action="{{ route('admin.products.update', $product['id']) }}" method="POST" class="row" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="old_image" value="{{ $product['image'] }}">
                            <div class="col-4">
                                <label>名称</label>
                                <input class="input mt-1" name="name" value="{{ $product['name'] }}" required>
                            </div>
                            <div class="col-2">
                                <label>价格</label>
                                <input class="input mt-1" name="price" type="number" step="0.01" min="0.01" value="{{ $product['price'] }}" required>
                            </div>
                            <div class="col-2">
                                <label>库存</label>
                                <input class="input mt-1" name="stock" type="number" min="0" value="{{ $product['stock'] }}" required>
                            </div>
                            <div class="col-2">
                                <label>状态</label>
                                <select class="select mt-1" name="status" required>
                                    <option value="active" @selected($product['status'] === 'active')>active</option>
                                    <option value="inactive" @selected($product['status'] === 'inactive')>inactive</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>图片路径（可选）</label>
                                <input class="input mt-1" name="image" value="{{ $product['image'] }}">
                            </div>
                            <div class="col-6">
                                <label>本地上传（可选）</label>
                                <input class="input mt-1" name="image_file" type="file" accept="image/*">
                                <div class="text-muted mt-1" style="font-size: 12px;">上传后会覆盖当前图片路径并写入 JSON。</div>
                            </div>
                            <div class="col-12">
                                <label>描述</label>
                                <input class="input mt-1" name="description" value="{{ $product['description'] }}" required>
                            </div>
                            <div class="col-12" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <button class="btn btn-success" type="submit">保存修改</button>
                            </div>
                        </form>

                        <form action="{{ route('admin.products.destroy', $product['id']) }}" method="POST" style="margin-top: 10px;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">删除商品</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection
