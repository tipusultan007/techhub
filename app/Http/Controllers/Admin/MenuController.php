<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Page;
use App\Models\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('allItems')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:50|unique:menus,location',
            'slug' => 'nullable|string|max:100|unique:menus,slug',
        ]);

        Menu::create([
            'name' => $request->name,
            'slug' => $request->slug ?: Str::slug($request->name),
            'location' => $request->location,
        ]);

        return back()->with('success', 'Menu created successfully.');
    }

    public function builder(Menu $menu)
    {
        $menu->load(['menuItems.childrenRecursive']);

        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::orderBy('name')->get();
        $pages = Page::orderBy('title')->get();
        $solutions = Solution::orderBy('title')->get();

        return view('admin.menus.builder', compact('menu', 'categories', 'brands', 'pages', 'solutions'));
    }

    public function addItem(Request $request, Menu $menu)
    {
        $request->validate([
            'type' => 'required|in:category,brand,page,custom,solution',
            'label' => 'required_if:type,custom',
            'url' => 'required_if:type,custom',
            'ids' => 'required_unless:type,custom|array',
        ]);

        if ($request->type === 'custom') {
            MenuItem::create([
                'menu_id' => $menu->id,
                'label' => $request->label,
                'url' => $request->url,
                'type' => 'custom',
                'order' => MenuItem::where('menu_id', $menu->id)->max('order') + 1,
            ]);
        } else {
            foreach ($request->ids as $id) {
                $label = '';
                switch ($request->type) {
                    case 'category':
                        $label = Category::find($id)->name;
                        break;
                    case 'brand':
                        $label = Brand::find($id)->name;
                        break;
                    case 'page':
                        $label = Page::find($id)->title;
                        break;
                    case 'solution':
                        $label = Solution::find($id)->title;
                        break;
                }

                MenuItem::create([
                    'menu_id' => $menu->id,
                    'label' => $label,
                    'type' => $request->type,
                    'model_id' => $id,
                    'order' => MenuItem::where('menu_id', $menu->id)->max('order') + 1,
                ]);
            }
        }

        return back()->with('success', 'Items added to menu.');
    }

    public function deleteItem(MenuItem $item)
    {
        $item->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Item removed from menu.']);
        }
        return back()->with('success', 'Item removed from menu.');
    }

    public function updateOrder(Request $request)
    {
        $items = json_decode($request->items, true);

        foreach ($items as $index => $itemData) {
            $this->saveItemHierarchy($itemData, null, $index);
        }

        return response()->json(['success' => true]);
    }

    private function saveItemHierarchy($itemData, $parentId, $order)
    {
        $item = MenuItem::find($itemData['id']);
        if ($item) {
            $item->update([
                'parent_id' => $parentId,
                'order' => $order,
            ]);

            if (isset($itemData['children'])) {
                foreach ($itemData['children'] as $childIndex => $childData) {
                    $this->saveItemHierarchy($childData, $item->id, $childIndex);
                }
            }
        }
    }

    public function updateItem(Request $request, MenuItem $item)
    {
        $request->validate([
            'label' => 'required|string',
            'url' => 'nullable|string',
            'target' => 'required|in:_self,_blank',
        ]);

        $item->update($request->only('label', 'url', 'target'));

        return back()->with('success', 'Item updated successfully.');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:menus,slug,' . $menu->id,
            'location' => 'nullable|string|max:50|unique:menus,location,' . $menu->id,
        ]);

        $menu->update($request->only('name', 'slug', 'location'));

        return back()->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return back()->with('success', 'Menu deleted successfully.');
    }
}
