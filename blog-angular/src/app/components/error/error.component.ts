import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-error',
  templateUrl: './error.component.html',
  styleUrls: ['./error.component.css']
})
export class ErrorComponent implements OnInit {
  public title: string;
  constructor() { 
    this.title = '404 Not Found'
  }

  ngOnInit(): void {
  }

}
