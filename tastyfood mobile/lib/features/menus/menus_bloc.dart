import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:equatable/equatable.dart';
import '../../core/models/models.dart';
import 'menus_repository.dart';

abstract class MenusEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class MenusLoadRequested extends MenusEvent {
  final String? category;
  final String? search;

  MenusLoadRequested({this.category, this.search});

  @override
  List<Object?> get props => [category, search];
}

class MenuToggleAvailabilityRequested extends MenusEvent {
  final int menuId;
  final bool available;

  MenuToggleAvailabilityRequested(this.menuId, this.available);

  @override
  List<Object?> get props => [menuId, available];
}

class MenuCreateRequested extends MenusEvent {
  final String name;
  final String description;
  final int price;
  final String category;
  final bool available;

  MenuCreateRequested({
    required this.name,
    required this.description,
    required this.price,
    required this.category,
    required this.available,
  });

  @override
  List<Object?> get props => [name, description, price, category, available];
}

class MenuUpdateRequested extends MenusEvent {
  final int menuId;
  final String name;
  final String description;
  final int price;
  final String category;
  final bool available;

  MenuUpdateRequested({
    required this.menuId,
    required this.name,
    required this.description,
    required this.price,
    required this.category,
    required this.available,
  });

  @override
  List<Object?> get props => [menuId, name, description, price, category, available];
}

class MenuDeleteRequested extends MenusEvent {
  final int menuId;

  MenuDeleteRequested(this.menuId);

  @override
  List<Object?> get props => [menuId];
}

abstract class MenusState extends Equatable {
  @override
  List<Object?> get props => [];
}

class MenusInitial extends MenusState {}

class MenusLoading extends MenusState {}

class MenusLoaded extends MenusState {
  final List<Menu> menus;

  MenusLoaded(this.menus);

  @override
  List<Object?> get props => [menus];
}

class MenusError extends MenusState {
  final String message;

  MenusError(this.message);

  @override
  List<Object?> get props => [message];
}

class MenusOperationSuccess extends MenusState {
  final String message;

  MenusOperationSuccess(this.message);

  @override
  List<Object?> get props => [message];
}

class MenusBloc extends Bloc<MenusEvent, MenusState> {
  final MenusRepository repository;
  String? _lastCategory;
  String? _lastSearch;

  MenusBloc(this.repository) : super(MenusInitial()) {
    on<MenusLoadRequested>(_onLoadRequested);
    on<MenuToggleAvailabilityRequested>(_onToggleAvailability);
    on<MenuCreateRequested>(_onCreateRequested);
    on<MenuUpdateRequested>(_onUpdateRequested);
    on<MenuDeleteRequested>(_onDeleteRequested);
  }

  Future<void> _onLoadRequested(
    MenusLoadRequested event,
    Emitter<MenusState> emit,
  ) async {
    _lastCategory = event.category;
    _lastSearch = event.search;
    emit(MenusLoading());
    try {
      final menus = await repository.getMenus(
        category: event.category,
        search: event.search,
      );
      emit(MenusLoaded(menus));
    } catch (e) {
      emit(MenusError(e.toString()));
    }
  }

  Future<void> _onToggleAvailability(
    MenuToggleAvailabilityRequested event,
    Emitter<MenusState> emit,
  ) async {
    try {
      await repository.toggleAvailability(event.menuId, event.available);
      add(MenusLoadRequested(category: _lastCategory, search: _lastSearch));
    } catch (e) {
      emit(MenusError(e.toString()));
    }
  }

  Future<void> _onCreateRequested(
    MenuCreateRequested event,
    Emitter<MenusState> emit,
  ) async {
    emit(MenusLoading());
    try {
      await repository.createMenu(
        name: event.name,
        description: event.description,
        price: event.price,
        category: event.category,
        available: event.available,
      );
      emit(MenusOperationSuccess('Menu berhasil ditambahkan'));
      add(MenusLoadRequested(category: _lastCategory, search: _lastSearch));
    } catch (e) {
      emit(MenusError(e.toString()));
    }
  }

  Future<void> _onUpdateRequested(
    MenuUpdateRequested event,
    Emitter<MenusState> emit,
  ) async {
    emit(MenusLoading());
    try {
      await repository.updateMenu(
        menuId: event.menuId,
        name: event.name,
        description: event.description,
        price: event.price,
        category: event.category,
        available: event.available,
      );
      emit(MenusOperationSuccess('Menu berhasil diperbarui'));
      add(MenusLoadRequested(category: _lastCategory, search: _lastSearch));
    } catch (e) {
      emit(MenusError(e.toString()));
    }
  }

  Future<void> _onDeleteRequested(
    MenuDeleteRequested event,
    Emitter<MenusState> emit,
  ) async {
    emit(MenusLoading());
    try {
      await repository.deleteMenu(event.menuId);
      emit(MenusOperationSuccess('Menu berhasil dihapus'));
      add(MenusLoadRequested(category: _lastCategory, search: _lastSearch));
    } catch (e) {
      emit(MenusError(e.toString()));
    }
  }
}
