package com.example.orders;

import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/orders")
public class OrderController {
    private final OrderRepository repo;

    public OrderController(OrderRepository repo) { this.repo = repo; }

    @GetMapping
    public List<Order> getAll(@RequestParam(required = false) String status) {
        return status != null ? repo.findByStatus(status) : repo.findAll();
    }

    @GetMapping("/{id}")
    public ResponseEntity<Order> getById(@PathVariable Long id) {
        return repo.findById(id).map(ResponseEntity::ok).orElse(ResponseEntity.notFound().build());
    }

    @PostMapping
    public Order create(@RequestBody Order order) {
        if (order.getStatus() == null) order.setStatus("PENDING");
        return repo.save(order);
    }

    @PutMapping("/{id}")
    public ResponseEntity<Order> update(@PathVariable Long id, @RequestBody Order updated) {
        return repo.findById(id).map(o -> {
            o.setCustomerName(updated.getCustomerName());
            o.setProduct(updated.getProduct());
            o.setQuantity(updated.getQuantity());
            o.setPrice(updated.getPrice());
            o.setStatus(updated.getStatus());
            return ResponseEntity.ok(repo.save(o));
        }).orElse(ResponseEntity.notFound().build());
    }

    @PatchMapping("/{id}/status")
    public ResponseEntity<Order> updateStatus(@PathVariable Long id, @RequestBody Map<String, String> body) {
        return repo.findById(id).map(o -> {
            o.setStatus(body.get("status"));
            return ResponseEntity.ok(repo.save(o));
        }).orElse(ResponseEntity.notFound().build());
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> delete(@PathVariable Long id) {
        if (!repo.existsById(id)) return ResponseEntity.notFound().build();
        repo.deleteById(id);
        return ResponseEntity.noContent().build();
    }
}
